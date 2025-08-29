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

        $divisions = Division::all();

        $employees = Employee::with('division', 'attendanceLogs')
            ->where('status', 'Active')
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
            })
            ->when($divisionId, function ($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->get();

        $workdaySetting = WorkdaySetting::first();
        if (!$workdaySetting) {
            return redirect()->route('settings.index')->with('error', 'Workday settings not found.');
        }

        $salaryDeduction = SalaryDeduction::first();
        $lateDeduction = $salaryDeduction->late_deduction ?? 0;
        $earlyDeduction = $salaryDeduction->early_deduction ?? 0;

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
        })->filter()->values()->all();

        return view('Superadmin.payroll.index', compact('payrolls', 'month', 'search', 'divisions'));
    }

    private function calculateFreelancePayroll($employee, $month, $cashAdvance)
    {
        try {
            $hourlyRate = $employee->division->hourly_rate ?? 0;

            $workDays = [];
            if ($employee->division && $employee->division->work_days) {
                if (is_string($employee->division->work_days)) {
                    $decoded = json_decode($employee->division->work_days, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $workDays = $decoded;
                    } else {
                        $workDays = explode(',', $employee->division->work_days);
                    }
                } elseif (is_array($employee->division->work_days)) {
                    $workDays = $employee->division->work_days;
                }
            }

            $plannedWorkDays = $this->calculateWorkdaysForMonth($workDays, $month, $employee);

            $logs = $employee->attendanceLogs()
                ->whereMonth('check_in', Carbon::parse($month)->month)
                ->whereYear('check_in', Carbon::parse($month)->year)
                ->get();

            $divisionIn  = $employee->division->check_in_time ?? '09:00:00';
            $divisionOut = $employee->division->check_out_time ?? '18:00:00';

            $standardIn  = Carbon::createFromFormat('H:i:s', $divisionIn)->format('H:i:s');
            $overtimeStart = Carbon::createFromFormat('H:i:s', $divisionOut)->addMinutes(30)->format('H:i:s'); // lembur dihitung 30 menit setelah jam pulang
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

                $isLate = $checkIn->gt($checkIn->copy()->setTimeFromTimeString($standardIn)->addMinutes($toleranceMinutes));
                if ($isLate) $lateCount++;

                $workDuration = $checkIn->diffInMinutes($checkOut);
                $workedHours = floor($workDuration / 60);
                $normalHours = min($workedHours, 8);

                if ($isLate) {
                    $normalHours = max(0, $normalHours - 1);
                }

                $totalNormalHours += $normalHours;

                $overtimeThreshold = $checkOut->copy()->startOfDay()->setTimeFromTimeString($overtimeStart);
                $overtime = 0;
                if ($checkOut->gt($overtimeThreshold)) {
                    $overtimeMinutes = $overtimeThreshold->diffInMinutes($checkOut);
                    $overtime = min(2, ceil($overtimeMinutes / 60));
                }

                $totalOvertimeHours += $overtime;

                \Log::info("PayrollLog | {$employee->first_name} {$employee->last_name} | {$workDate} | In: {$checkIn->format('H:i')} | Out: {$checkOut->format('H:i')} | NormalHours: {$normalHours} | Overtime: {$overtime} | Telat: " . ($isLate ? 'Ya' : 'Tidak'));
            }

            $workedDays = count($uniqueWorkDays);
            $absentDays = max(0, $plannedWorkDays - $workedDays);

            $baseSalary = $totalNormalHours * $hourlyRate;
            $overtimePay = $totalOvertimeHours * $hourlyRate;
            $totalSalary = $baseSalary + $overtimePay - $cashAdvance;

            \Log::info("Payroll Summary | {$employee->first_name} {$employee->last_name} | Month: {$month} | Planned WorkDays: {$plannedWorkDays} | Actual WorkDays: {$workedDays} | Absent: {$absentDays} | Total Normal Hours: {$totalNormalHours} | Overtime Hours: {$totalOvertimeHours} | Base Salary: {$baseSalary} | Overtime Pay: {$overtimePay} | Total Salary: {$totalSalary}");

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
                $workedDays,
                $absentDays,
                0,
                $workedDays,
                $lateCount,
                $cashAdvance
            );
        } catch (\Exception $e) {
            \Log::error("Error Payroll Freelance | Employee: {$employee->employee_id} | {$employee->first_name} {$employee->last_name} | Month: {$month} | Msg: {$e->getMessage()} | Trace: {$e->getTraceAsString()}");
            return null;
        }
    }

    private function calculatePermanentPayroll($employee, $month, $workdaySetting, $lateDeduction, $earlyDeduction, $cashAdvance)
    {
        try {
            $recap = AttandanceRecap::where('employee_id', $employee->employee_id)
                ->where('month', $month)
                ->first();

            $totalDaysWorked = $recap->total_present ?? 0;
            $totalLateCheckIn = $recap->total_late ?? 0;
            $totalEarlyCheckOut = $recap->total_early ?? 0;

            $divisionWorkDays = [];
            if ($employee->division && $employee->division->work_days) {
                if (is_string($employee->division->work_days)) {
                    $decoded = json_decode($employee->division->work_days, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $divisionWorkDays = $decoded;
                    } else {
                        $divisionWorkDays = explode(',', $employee->division->work_days);
                    }
                } elseif (is_array($employee->division->work_days)) {
                    $divisionWorkDays = $employee->division->work_days;
                }
            }

            if (empty($divisionWorkDays)) {
                $divisionWorkDays = $workdaySetting->effective_days ?? [];
            }

            $monthlyWorkdays = $this->calculateWorkdaysForMonth($divisionWorkDays, $month, $employee);

            $totalAbsent = max(0, $monthlyWorkdays - $totalDaysWorked);

            $monthlyWorkdays = $this->calculateWorkdaysForMonth($divisionWorkDays, $month, $employee);

            $dailySalary = $monthlyWorkdays > 0 ? $employee->current_salary / $monthlyWorkdays : 0;
            $divisionIn  = $employee->division->check_in_time ?? '09:00:00';
            $divisionOut = $employee->division->check_out_time ?? '18:00:00';

            $workDurationInHours = Carbon::createFromFormat('H:i:s', $divisionOut)
                ->diffInHours(Carbon::createFromFormat('H:i:s', $divisionIn));

            $hourlyRate = $dailySalary / ($workDurationInHours ?: 1);

            $overtimeData = Overtime::where('employee_id', $employee->employee_id)
                ->where('status', 'approved')
                ->get();
            $totalOvertimeHours = $overtimeData->sum('duration');
            $overtimePay = $totalOvertimeHours * $hourlyRate;

            $totalDeductions = ($totalLateCheckIn * $lateDeduction) + ($totalEarlyCheckOut * $earlyDeduction);
            $baseSalary = $employee->current_salary ?? 0;
            $totalSalary = $baseSalary - $totalDeductions + $overtimePay - $cashAdvance;

            $existing = Payroll::where('employee_id', $employee->employee_id)
                ->where('month', $month)
                ->first();
            $cashAdvance = $existing->cash_advance ?? 0;

            $divisionName = strtolower((string) optional($employee->division)->name);
            if (in_array($divisionName, ['supir', 'kenek', 'helper', 'teknisi ac'])) {
                $weeklyData = $this->calculateWeeklyWorkdays($employee, $divisionWorkDays, $month);

                $attendanceAllowance = $employee->attendance_allowance ?? 0;
                if ($attendanceAllowance > 0) {
                    $attendanceDeduction = $this->calculateAttendanceAllowance(
                        $employee,
                        $weeklyData,
                        $attendanceAllowance
                    );

                    // allowance bersih = allowance - potongan
                    $finalAllowance = $attendanceAllowance - $attendanceDeduction;

                    // tambahkan ke totalSalary
                    $totalSalary += $finalAllowance;

                    \Log::info("AttendanceAllowance | {$employee->first_name} {$employee->last_name} | Allowance: {$attendanceAllowance} | Deduction: {$attendanceDeduction} | Final Allowance: {$finalAllowance}");
                }
            }

            \Log::info("Payroll Permanent Summary | {$employee->first_name} {$employee->last_name} | Month: {$month} | Workdays: {$monthlyWorkdays} | Worked: {$totalDaysWorked} | Absent: {$totalAbsent} | Base: {$baseSalary} | OT Pay: {$overtimePay} | Total: {$totalSalary}");

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
        } catch (\Exception $e) {
            \Log::error("Payroll Permanent ERROR | Employee ID: {$employee->employee_id} | Name: {$employee->first_name} {$employee->last_name} | Month: {$month} | Error: {$e->getMessage()} | File: {$e->getFile()} | Line: {$e->getLine()}");
            \Log::error($e->getTraceAsString());
            return null;
        }
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
    private function calculateWorkdaysForMonth(array $effectiveDays, string $month, $employee = null): int
    {
        [$year, $monthNumber] = explode('-', $month);
        $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $monthNumber, 1)->endOfMonth();
    
        // Ambil libur nasional
        $holidayDates = Event::where('category', 'danger')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get()
            ->flatMap(fn($event) => CarbonPeriod::create($event->start_date, $event->end_date)->toArray())
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->toArray();
    
        $period = CarbonPeriod::create($startDate, $endDate);
        $division = strtolower((string) optional($employee->division)->name);
    
        // --- Precompute khusus Kasir: 2 weekday pertama libur ---
        $kasirExtraOff = [];
        if ($division === 'kasir') {
            $kasirExtraOff = collect(CarbonPeriod::create($startDate, $endDate))
                ->filter(fn($d) => in_array($d->format('l'), ['Monday','Tuesday','Wednesday','Thursday','Friday']))
                ->take(2)
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray();
        }
    
        $workdays = collect($period)->filter(function ($date) use ($effectiveDays, $holidayDates, $employee, $division, $kasirExtraOff) {
            $dayName = $date->format('l');
            $dateStr = $date->format('Y-m-d');
    
            // Log awal
            \Log::debug("[CHECK-DIVISION] {$employee->first_name} {$employee->last_name} | Division: {$division} | Day: {$dayName} | Date: {$dateStr}");
    
            // Division khusus: tambahkan Sunday kalau perlu
            if (in_array($division, ['supir', 'teknisi ac', 'kenek', 'helper']) && !in_array('Sunday', $effectiveDays)) {
                $effectiveDays[] = 'Sunday';
            }
    
            // Base workday check
            $isWorkday = in_array($dayName, $effectiveDays) && !in_array($dateStr, $holidayDates);
    
            if (!$isWorkday && in_array($dateStr, $holidayDates)) {
                \Log::debug("[DEBUG] Skip National Holiday: {$dateStr} ({$dayName})");
            }
    
            // --- Supir & Teknisi AC: skip Minggu genap ---
            if ($isWorkday && in_array($division, ['supir', 'teknisi ac'])) {
                if ($dayName === 'Sunday' && $date->weekOfMonth % 2 == 0) {
                    \Log::debug("[DEBUG] Skip Sunday Genap ({$division}): {$dateStr}");
                    $isWorkday = false;
                }
            }
    
            // --- Kenek & Helper: skip Minggu terakhir ---
            if ($isWorkday && in_array($division, ['kenek', 'helper'])) {
                if ($dayName === 'Sunday') {
                    $lastSunday = Carbon::create($date->year, $date->month, 1)
                        ->endOfMonth()
                        ->previous('Sunday')
                        ->format('Y-m-d');
                    if ($dateStr === $lastSunday) {
                        \Log::debug("[DEBUG] Skip Last Sunday ({$division}): {$dateStr}");
                        $isWorkday = false;
                    }
                }
            }
    
            // --- Kasir: 2 weekday libur tiap bulan ---
            if ($isWorkday && $division === 'kasir' && in_array($dateStr, $kasirExtraOff)) {
                \Log::debug("[DEBUG] Kasir extra weekday off: {$dateStr}");
                $isWorkday = false;
            }
    
            if ($isWorkday) {
                \Log::debug("[DEBUG] Workday: {$dateStr} ({$dayName})");
            }
    
            return $isWorkday;
        });
    
        return $workdays->count();
    }
    


    private function calculateWeeklyWorkdays($employee, array $effectiveDays, string $month): array
    {
        [$year, $monthNumber] = explode('-', $month);
        $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $monthNumber, 1)->endOfMonth();

        // Ambil tanggal libur nasional
        $holidayDates = Event::where('category', 'danger')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get()
            ->flatMap(fn($event) => CarbonPeriod::create($event->start_date, $event->end_date)->toArray())
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->toArray();

        // Ambil absensi pegawai
        $attendances = $employee->attendanceLogs()
            ->whereMonth('check_in', $monthNumber)
            ->whereYear('check_in', $year)
            ->get()
            ->groupBy(fn($log) => Carbon::parse($log->check_in)->format('Y-m-d'));

        $weeklyData = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        $division = strtolower((string) optional($employee->division)->name);

        foreach ($period as $date) {
            $dayName = $date->format('l');
            $weekNum = $date->weekOfMonth;
            $dateStr = $date->format('Y-m-d');

            // cek hari kerja
            if (!in_array($dayName, $effectiveDays)) continue;
            if (in_array($dateStr, $holidayDates)) continue;

            // Supir & Teknisi AC → skip Minggu genap
            if (in_array($division, ['supir', 'teknisi ac']) && $dayName === 'Sunday' && $weekNum % 2 === 0) {
                \Log::debug("[SKIP] {$employee->first_name} {$employee->last_name} skip Sunday genap {$dateStr}");
                continue;
            }

            // Kenek & Helper → skip Minggu terakhir
            if (in_array($division, ['kenek', 'helper']) && $dayName === 'Sunday') {
                $lastSunday = Carbon::create($date->year, $date->month, 1)
                    ->endOfMonth()
                    ->previous('Sunday')
                    ->format('Y-m-d');
                if ($dateStr === $lastSunday) {
                    \Log::debug("[SKIP] {$employee->first_name} {$employee->last_name} skip last Sunday {$dateStr}");
                    continue; // ❌ jangan masukkan sama sekali
                }
            }

            // Tentukan hadir/tidak hadir
            $attended = isset($attendances[$dateStr]);
            $weeklyData[$weekNum][$dayName] = $attended;

            \Log::debug("[WeeklyData] {$employee->first_name} {$employee->last_name} | {$dateStr} ({$dayName}) | Week {$weekNum} | Attended: " . ($attended ? 'Yes' : 'No'));
        }

        return $weeklyData;
    }


    private function calculateAttendanceAllowance($employee, $weeklyData, $baseAllowance)
    {
        // Bagi 4 minggu @ 50k, AllFull 100k (untuk baseAllowance 300k)
        $weekShare = $baseAllowance / 6; // contoh 50k
        $shares = [
            1 => $weekShare,
            2 => $weekShare,
            3 => $weekShare,
            4 => $weekShare,
        ];
        $allFull = $baseAllowance - array_sum($shares); // contoh 100k

        $lostWeeks = [];       // set of week numbers to deduct
        $loseAllFull = false;  // only once

        // Debug bantu (opsional)
        // \Log::debug("[ALLOWANCE] Base={$baseAllowance}, share={$weekShare}, allFull={$allFull}");

        foreach ($weeklyData as $weekNum => $days) {
            // Abaikan week di luar 1..4 jika weeklyData punya week 5 (mis. akhir bulan)
            if (!isset($shares[$weekNum])) {
                // Jika ingin week5 mempengaruhi allFull, boleh diaktifkan:
                // if (count(array_filter($days, fn($att) => !$att)) > 0) $loseAllFull = true;
                continue;
            }

            $absentCount = count(array_filter($days, fn($attended) => !$attended));

            if ($absentCount > 0) {
                // Kehilangan share minggu ini + All Full
                $lostWeeks[$weekNum] = true;
                $loseAllFull = true;

                // Jika absen >= 2 → hilang share minggu berikutnya juga (jika ada)
                if ($absentCount >= 2) {
                    $nextWeek = $weekNum + 1;
                    if (isset($shares[$nextWeek])) {
                        $lostWeeks[$nextWeek] = true;
                    }
                }
            }

            // Debug (opsional)
            // \Log::debug("[ALLOWANCE] Week {$weekNum} absent={$absentCount} | LostWeeks=" . json_encode(array_keys($lostWeeks)));
        }

        // Hitung potongan akhir
        $deduction = 0;

        // potong shares minggu yang hilang (tanpa double count)
        foreach (array_keys($lostWeeks) as $w) {
            $deduction += $shares[$w];
        }

        // potong All Full sekali saja jika ada minimal satu pelanggaran
        if ($loseAllFull) {
            $deduction += $allFull;
        }

        return min($deduction, $baseAllowance);
    }
}
