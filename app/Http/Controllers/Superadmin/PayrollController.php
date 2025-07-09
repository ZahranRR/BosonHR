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
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

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

        $employees = Employee::where('status', 'Active')->get();
        $workdaySetting = WorkdaySetting::first();

        if (!$workdaySetting) {
            return redirect()->route('settings.index')->with('error', 'Workday settings not found.');
        }

        $salaryDeduction = SalaryDeduction::first();
        $lateDeduction = $salaryDeduction ? $salaryDeduction->late_deduction : 0;
        $earlyDeduction = $salaryDeduction ? $salaryDeduction->early_deduction : 0;

        $payrolls = $employees
            ->filter(function ($employee) use ($search) {
                // Filter berdasarkan pencarian nama karyawan jika ada input search
                if ($search) {
                    return stripos($employee->first_name . ' ' . $employee->last_name, $search) !== false;
                }
                return true;
            })
            ->map(function ($employee) use ($month, $lateDeduction, $earlyDeduction, $workdaySetting) {
                // Proses perhitungan payroll tetap sama
                $salary = $employee->current_salary;

                $recap = AttandanceRecap::where('employee_id', $employee->employee_id)->where('month', $month)->first();

                $totalDaysWorked = $recap ? $recap->total_present : 0;
                $totalLateCheckIn = $recap ? $recap->total_late : 0;
                $totalEarlyCheckOut = $recap ? $recap->total_early : 0;
                $totalAbsent = $recap ? $recap->total_absent : 0;

                $totalDaysOff = Offrequest::where('user_id', $employee->user_id)
                    ->where('status', 'approved')
                    ->whereYear('start_event', Carbon::parse($month)->year)
                    ->whereMonth('start_event', Carbon::parse($month)->month)
                    ->get()
                    ->sum(function ($off) {
                        $start = Carbon::parse($off->start_event)->startOfDay();
                        $end = Carbon::parse($off->end_event)->endOfDay();
                        return (int) $start->diffInDays($end) + 1;
                    });

                $effectiveDays = $workdaySetting->effective_days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                $monthlyWorkdays = $this->calculateWorkdaysForMonth($effectiveDays, $month);

                $effectiveWorkDays = $monthlyWorkdays;

                $workDurationInHours = $employee->check_in_time && $employee->check_out_time ? Carbon::parse($employee->check_in_time)->diffInHours(Carbon::parse($employee->check_out_time)) : 8;

                $dailySalary = $monthlyWorkdays > 0 ? $employee->current_salary / $monthlyWorkdays : 0;
                $hourlyRate = $workDurationInHours > 0 ? $dailySalary / $workDurationInHours : 0;

                $overtimeData = Overtime::where('employee_id', $employee->employee_id)->where('status', 'approved')->get();
                $totalOvertimeHours = $overtimeData->sum('duration');

                $overtimePay = $totalOvertimeHours * $hourlyRate;

                $totalLateDeduction = $totalLateCheckIn * $lateDeduction;
                $totalEarlyDeduction = $totalEarlyCheckOut * $earlyDeduction;
                $totalDeductions = $totalLateDeduction + $totalEarlyDeduction;

                $baseSalary = $totalDaysWorked * $dailySalary;
                $totalSalary = $baseSalary - $totalDeductions + $overtimePay;

                // Simpan ke database atau update jika sudah ada
                Payroll::updateOrCreate(
                    [
                        'employee_id' => $employee->employee_id,
                        'month' => $month,
                    ],
                    [
                        'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                        'total_days_worked' => $totalDaysWorked,
                        'total_absent' => $totalAbsent,
                        'total_days_off' => $totalDaysOff,
                        'total_late_check_in' => $totalLateCheckIn,
                        'total_early_check_out' => $totalEarlyCheckOut,
                        'effective_work_days' => $effectiveWorkDays,
                        'current_salary' => $salary,
                        'overtime_pay' => $overtimePay,
                        'total_salary' => $totalSalary,
                        'status' => 'Pending',
                    ],
                );

                return [
                    'id' => $employee->employee_id,
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'current_salary' => $salary,
                    'total_days_worked' => $totalDaysWorked,
                    'total_days_off' => $totalDaysOff,
                    'total_absent' => $totalAbsent,
                    'total_late_check_in' => $totalLateCheckIn,
                    'total_early_check_out' => $totalEarlyCheckOut,
                    'effective_work_days' => $effectiveWorkDays,
                    'overtime_pay' => $overtimePay,
                    'total_salary' => $totalSalary,
                    'status' => 'Pending',
                ];
            });

        return view('Superadmin.payroll.index', compact('payrolls', 'month', 'search'));
    }
    
    private function calculateWorkdaysForMonth(array $effectiveDays, string $month): int
{
    [$year, $monthNumber] = explode('-', $month);

    // Ambil tanggal mulai dan akhir bulan
    $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
    $endDate = Carbon::create($year, $monthNumber, 1)->endOfMonth();

    // Ambil semua tanggal libur dari tabel event (kategori 'danger')
    $holidayDates = Event::where('category', 'danger')
        ->whereBetween('start_date', [$startDate, $endDate])
        ->get()
        ->flatMap(function ($event) {
            return CarbonPeriod::create($event->start_date, $event->end_date)->toArray();
        })
        ->map(fn ($date) => $date->format('Y-m-d'))
        ->unique()
        ->toArray();

    // Iterasi tanggal dalam bulan dan hitung hari kerja
    $period = CarbonPeriod::create($startDate, $endDate);
    $workdays = collect($period)->filter(function ($date) use ($effectiveDays, $holidayDates) {
        return in_array($date->format('l'), $effectiveDays) && !in_array($date->format('Y-m-d'), $holidayDates);
    });

    return $workdays->count();
}



    public function approve($id)
    {
        // Mencari payroll berdasarkan ID
        $payroll = Payroll::find($id);

        // Mengecek apakah payroll ditemukan dan statusnya 'pending'
        if ($payroll && $payroll->status === 'pending') {
            // Mengubah status menjadi 'approved'
            $payroll->status = 'approved';
            $payroll->save(); // Menyimpan perubahan ke database

            // Mengirim pesan sukses setelah berhasil approve
            return redirect()->route('payroll.index')->with('success', 'Payroll data approved!');
        }

        // Jika tidak ditemukan atau sudah approve
        return redirect()->route('payroll.index')->with('error', 'Payroll data already approved or not found.');
    }

    public function exportToCsv()
    {
        $payrolls = Payroll::where('status', 'approved')->get();

        // Debugging payroll data jika ada data kosong
        if ($payrolls->isEmpty()) {
            return redirect()->back()->with('error', 'No approved payroll data available.');
        }

        $csvHeader = ['Employee Name', 'Current Salary', 'Total Days Worked', 'Total Days Off', 'Total Absent', 'Total Late Check In', 'Total Early Check Out', 'Effective Work Days', 'Overtime Pay', 'Total Salary', 'Status'];

        $csvData = $payrolls->map(function ($payroll) {
            return [$payroll->employee_name, $payroll->current_salary, $payroll->total_days_worked, $payroll->total_days_off, $payroll->total_absent, $payroll->total_late_check_in, $payroll->total_early_check_out, $payroll->effective_work_days, $payroll->overtime_pay, $payroll->total_salary, $payroll->status];
        });

        $filename = 'payroll_approved_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return Response::stream(
            function () use ($csvHeader, $csvData) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $csvHeader);
                foreach ($csvData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            },
            200,
            $headers,
        );
    }
}
