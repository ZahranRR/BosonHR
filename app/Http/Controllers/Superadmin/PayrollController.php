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
            $existingPayroll = Payroll::where('employee_id', $employee->employee_id)
                ->where('month', $month)
                ->first();
            $cashAdvance = $existingPayroll->cash_advance ?? 0;

            if ($employee->employee_type === 'Freelance') {
                return $this->calculateFreelancePayroll($employee, $month, $cashAdvance);
            } else {
                return $this->calculatePermanentPayroll($employee, $month, $workdaySetting, $lateDeduction, $earlyDeduction, $cashAdvance);
            }
        })->filter()->values()->all();/////////////////////////////////////////////

        // Kirim semua data ke view
        return view('Superadmin.payroll.index', compact('payrolls', 'month', 'search', 'divisions'));
    }


    private function calculateFreelancePayroll($employee, $month, $cashAdvance)
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
        $totalSalary = $baseSalary + $overtimePay - $cashAdvance;

        \Log::info("Payroll Summary | {$employee->name} | Month: {$month} | WorkDays: " . count($uniqueWorkDays) . " | Total Normal Hours: {$totalNormalHours} | Overtime Hours: {$totalOvertimeHours} | Base Salary: {$baseSalary} | Overtime Pay: {$overtimePay} | Total Salary: {$totalSalary}");

        $existing = Payroll::where('employee_id', $employee->employee_id) //tadinya $employee->employee_id
            ->where('month', $month)
            ->first();
        $cashAdvance = $existing->cash_advance ?? 0;

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
            $lateCount,
            $cashAdvance
        );
    }


    private function calculatePermanentPayroll($employee, $month, $workdaySetting, $lateDeduction, $earlyDeduction, $cashAdvance)
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
        $totalSalary = $baseSalary - $totalDeductions + $overtimePay - $cashAdvance;

        $existing = Payroll::where('employee_id', $employee->employee_id)
            ->where('month', $month)
            ->first();
        $cashAdvance = $existing->cash_advance ?? 0;

        return $this->storePayroll(
            $employee,
            $month,
            $totalSalary,
            $baseSalary,
            $overtimePay,
            $totalDaysWorked,
            $totalAbsent,
            $totalEarlyCheckOut,
            $monthlyWorkdays,
            $totalLateCheckIn,
            $cashAdvance
        );
    }
    private function storePayroll($employee, $month, $totalSalary, $baseSalary, $overtimePay, $totalDaysWorked, $totalAbsent, $totalEarlyCheckOut, $effectiveWorkDays, $totalLateCheckIn, $cashAdvance)
    {
        $isFreelance = $employee->employee_type === 'Freelance';

        $payroll = Payroll::updateOrCreate(
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
                'cash_advance' => $cashAdvance,
                'total_salary' => $totalSalary,
                'status' => 'Pending',
            ]
        );

        return [
            'payroll_id' => $payroll->payroll_id,
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
            'cash_advance' => $cashAdvance,
            'total_salary' => $totalSalary,
            'status' => 'Pending',
        ];
    }

    public function updateCashAdvance(Request $request, $id)
    {
        try {
            $request->validate([
                'cash_advance' => 'required|numeric|min:0',
            ]);

            $payroll = Payroll::findOrFail($id);
            $oldValue = $payroll->cash_advance;

            $payroll->cash_advance = $request->cash_advance;
            $payroll->save();

            Log::info("Cash Advance updated", [
                'payroll_id' => $id,
                'old_value' => $oldValue,
                'new_value' => $payroll->cash_advance,
                'user_id' => auth()->id(), // jika pakai auth
            ]);

            return response()->json([
                'message' => 'Cash advance updated successfully.',
                'cash_advance' => number_format($payroll->cash_advance, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update cash advance', [
                'payroll_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(), // optional
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan kasbon.',
            ], 500);
        }
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
