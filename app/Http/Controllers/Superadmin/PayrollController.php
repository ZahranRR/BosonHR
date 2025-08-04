<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AttandanceRecap;
use App\Models\Employee;
use App\Models\Offrequest;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\SalaryDeduction;
use App\Models\WorkdaySetting;
use App\Models\Event;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payroll.index')->only(['index', 'approve']);
        $this->middleware('permission:payroll.export')->only(['exportToCsv']);
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $month = $request->query('month', now()->format('Y-m'));
        $divisionId = $request->query('division');

        // Ambil daftar division untuk dropdown
        $divisions = Division::all();

        // Ambil semua employee aktif + relasi yang dibutuhkan
        $employees = Employee::with('division', 'attendanceLogs')
            ->where('status', 'Active')
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
            })
            ->when($divisionId, function ($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->get();

        // Ambil setting jam kerja
        $workdaySetting = WorkdaySetting::first();
        if (!$workdaySetting) {
            return redirect()->route('settings.index')->with('error', 'Workday settings not found.');
        }

        // Ambil setting pemotongan gaji
        $salaryDeduction = SalaryDeduction::first();
        $lateDeduction = $salaryDeduction->late_deduction ?? 0;
        $earlyDeduction = $salaryDeduction->early_deduction ?? 0;

        // Proses payroll untuk semua employee
        $payrolls = $employees->map(function ($employee) use ($month, $workdaySetting, $lateDeduction, $earlyDeduction) {
            if ($employee->employee_type === 'Freelance') {
                return $this->calculateFreelancePayroll($employee, $month);
            } else {
                return $this->calculatePermanentPayroll($employee, $month, $workdaySetting, $lateDeduction, $earlyDeduction);
            }
        })->filter();

        // Kirim semua data ke view
        return view('Superadmin.payroll.index', compact('payrolls', 'month', 'search', 'divisions'));
    }


    private function calculateFreelancePayroll($employee, $month)
    {
        $hourlyRate = $employee->division->hourly_rate ?? 0;

        $logs = $employee->attendanceLogs()
            ->whereMonth('check_in', Carbon::parse($month)->month)
            ->whereYear('check_in', Carbon::parse($month)->year)
            ->get();

        $standardIn = '09:00';
        $overtimeStart = '18:30';
        $toleranceMinutes = 15;

        $totalNormalHours = 0;
        $totalOvertimeHours = 0;
        $lateCount = 0;
        $uniqueWorkDays = [];

        foreach ($logs as $log) {
            if (!$log->check_in || !$log->check_out) continue;

            $checkIn = Carbon::parse($log->check_in);
            $checkOut = Carbon::parse($log->check_out);
            $workDate = $checkIn->toDateString();
            $uniqueWorkDays[$workDate] = true;

            // Hitung telat
            $isLate = $checkIn->gt($checkIn->copy()->setTimeFromTimeString($standardIn)->addMinutes($toleranceMinutes));
            if ($isLate) $lateCount++;

            // Normal hours
            $workDuration = $checkIn->diffInMinutes($checkOut);
            $workedHours = floor($workDuration / 60);
            
            // Batasi maksimal jam kerja normal = 8
            $normalHours = min($workedHours, 8);
            
            // Jika telat, kurangi 1 jam normal (jangan melebihi jam kerja aktual)
            if ($isLate) {
                $normalHours = max(0, $normalHours - 1);
            }
            

            $totalNormalHours += $normalHours;

            // Hitung overtime
            $overtimeThreshold = $checkOut->copy()->startOfDay()->setTimeFromTimeString($overtimeStart);
            if ($checkOut->gt($overtimeThreshold)) {
                $overtimeMinutes = $overtimeThreshold->diffInMinutes($checkOut);
                $overtime = min(2, ceil($overtimeMinutes / 60)); // max 2 jam lembur
            } else {
                $overtime = 0;
            }


            $totalOvertimeHours += $overtime;

            // Log
            \Log::info("PayrollLog | {$employee->name} | {$workDate} | In: {$checkIn->format('H:i')} | Out: {$checkOut->format('H:i')} | NormalHours: {$normalHours} | Overtime: {$overtime} | Telat: " . ($isLate ? 'Ya' : 'Tidak'));
        }

        $baseSalary = $totalNormalHours * $hourlyRate;
        $overtimePay = $totalOvertimeHours * $hourlyRate;
        $totalSalary = $baseSalary + $overtimePay;

        \Log::info("Payroll Summary | {$employee->name} | Month: {$month} | WorkDays: " . count($uniqueWorkDays) . " | Total Normal Hours: {$totalNormalHours} | Overtime Hours: {$totalOvertimeHours} | Base Salary: {$baseSalary} | Overtime Pay: {$overtimePay} | Total Salary: {$totalSalary}");

        return $this->storePayroll(
            $employee,
            $month,
            $totalSalary,
            $baseSalary,
            $overtimePay,
            count($uniqueWorkDays),
            0, // Absent
            0, // Early checkout
            count($uniqueWorkDays), // Effective days
            $lateCount
        );
    }


    private function calculatePermanentPayroll($employee, $month, $workdaySetting, $lateDeduction, $earlyDeduction)
    {
        $recap = AttandanceRecap::where('employee_id', $employee->employee_id)->where('month', $month)->first();

        $totalDaysWorked = $recap->total_present ?? 0;
        $totalLateCheckIn = $recap->total_late ?? 0;
        $totalEarlyCheckOut = $recap->total_early ?? 0;
        $totalAbsent = $recap->total_absent ?? 0;

        $monthlyWorkdays = $this->calculateWorkdaysForMonth($workdaySetting->effective_days ?? [], $month);

        $dailySalary = $monthlyWorkdays > 0 ? $employee->current_salary / $monthlyWorkdays : 0;
        $workDurationInHours = Carbon::parse($employee->check_out_time)->diffInHours(Carbon::parse($employee->check_in_time));
        $hourlyRate = $dailySalary / ($workDurationInHours ?: 1);

        $overtimeData = Overtime::where('employee_id', $employee->employee_id)->where('status', 'approved')->get();
        $totalOvertimeHours = $overtimeData->sum('duration');
        $overtimePay = $totalOvertimeHours * $hourlyRate;

        $totalDeductions = ($totalLateCheckIn * $lateDeduction) + ($totalEarlyCheckOut * $earlyDeduction);
        $baseSalary = $totalDaysWorked * $dailySalary;
        $totalSalary = $baseSalary - $totalDeductions + $overtimePay;

        return $this->storePayroll($employee, $month, $totalSalary, $baseSalary, $overtimePay, $totalDaysWorked, $totalAbsent, $totalEarlyCheckOut, $monthlyWorkdays, $totalLateCheckIn);
    }

    private function storePayroll($employee, $month, $totalSalary, $baseSalary, $overtimePay, $totalDaysWorked, $totalAbsent, $totalEarlyCheckOut, $effectiveWorkDays, $totalLateCheckIn)
    {
        $isFreelance = $employee->employee_type === 'Freelance';

        Payroll::updateOrCreate(
            ['employee_id' => $employee->employee_id, 'month' => $month],
            [
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'current_salary' => $isFreelance ? 0 : ($employee->current_salary ?? 0),
                'total_days_worked' => $totalDaysWorked,
                'total_absent' => $totalAbsent,
                'total_days_off' => 0,
                'total_late_check_in' => $totalLateCheckIn,
                'total_early_check_out' => $totalEarlyCheckOut,
                'effective_work_days' => $effectiveWorkDays,
                'overtime_pay' => $overtimePay,
                'total_salary' => $totalSalary,
                'status' => 'Pending',
            ]
        );

        return [
            'id' => $employee->employee_id,
            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
            'current_salary' => $isFreelance ? 0 : ($employee->current_salary ?? 0),
            'total_days_worked' => $totalDaysWorked,
            'total_absent' => $totalAbsent,
            'total_days_off' => 0,
            'total_late_check_in' => $totalLateCheckIn,
            'total_early_check_out' => $totalEarlyCheckOut,
            'effective_work_days' => $effectiveWorkDays,
            'overtime_pay' => $overtimePay,
            'total_salary' => $totalSalary,
            'status' => 'Pending',
        ];
    }


    private function calculateWorkdaysForMonth(array $effectiveDays, string $month): int
    {
        [$year, $monthNumber] = explode('-', $month);

        $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $endDate = Carbon::create($year, $monthNumber, 1)->endOfMonth();

        $holidayDates = Event::where('category', 'danger')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get()
            ->flatMap(function ($event) {
                return CarbonPeriod::create($event->start_date, $event->end_date)->toArray();
            })
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->toArray();

        $period = CarbonPeriod::create($startDate, $endDate);
        $workdays = collect($period)->filter(function ($date) use ($effectiveDays, $holidayDates) {
            return in_array($date->format('l'), $effectiveDays) && !in_array($date->format('Y-m-d'), $holidayDates);
        });

        return $workdays->count();
    }
}
