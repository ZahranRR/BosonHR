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

            }

            $totalOvertimeHours = Overtime::where('employee_id', $employee->employee_id)
                ->where('status', 'approved')
                ->whereMonth('overtime_date', Carbon::parse($month)->month)
                ->whereYear('overtime_date', Carbon::parse($month)->year)
                ->sum('duration');

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

            $totalDaysWorked   = $recap->total_present ?? 0;
            $totalLateCheckIn  = $recap->total_late ?? 0;
            $totalEarlyCheckOut = $recap->total_early ?? 0;

            // --- Ambil hari kerja ---
            $divisionWorkDays = [];
            if ($employee->division && $employee->division->work_days) {
                if (is_string($employee->division->work_days)) {
                    $decoded = json_decode($employee->division->work_days, true);
                    $divisionWorkDays = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $employee->division->work_days);
                } elseif (is_array($employee->division->work_days)) {
                    $divisionWorkDays = $employee->division->work_days;
                }
            }
            if (empty($divisionWorkDays)) {
                $divisionWorkDays = $workdaySetting->effective_days ?? [];
            }

            $monthlyWorkdays = $this->calculateWorkdaysForMonth($divisionWorkDays, $month, $employee);
            $totalAbsent = max(0, $monthlyWorkdays - $totalDaysWorked);

            // --- Hitung daily salary & hourly rate ---
            $dailySalary = $monthlyWorkdays > 0 ? $employee->current_salary / $monthlyWorkdays : 0;

            $divisionIn  = $employee->division->check_in_time ?? '09:00:00';
            $divisionOut = $employee->division->check_out_time ?? '18:00:00';

            $in  = Carbon::createFromFormat('H:i:s', $divisionIn);
            $out = Carbon::createFromFormat('H:i:s', $divisionOut);

            $workDurationInHours = max(1, $out->diffInHours($in)); // ⛔ tidak boleh 0
            $hourlyRate = $employee->division->hourly_rate ?? ($dailySalary / $workDurationInHours);

            // --- Ambil overtime dari table Overtime ---
            $overtimeData = Overtime::where('employee_id', $employee->employee_id)
                ->where('status', 'approved')
                ->whereMonth('overtime_date', Carbon::parse($month)->month)
                ->whereYear('overtime_date', Carbon::parse($month)->year)
                ->get();

            $totalOvertimeHours = 0;

            foreach ($overtimeData as $ot) {
                $attendance = $employee->attendanceLogs()
                    ->whereDate('check_in', $ot->overtime_date)
                    ->orderByDesc('check_out')
                    ->first();

                if (!$attendance || !$attendance->check_out) {
                    \Log::debug("[OT] {$employee->first_name} {$employee->last_name} | {$ot->overtime_date} | Skip (no checkout)");
                    continue;
                }

                $checkOut = Carbon::parse($attendance->check_out);
                $divisionOutTime = Carbon::createFromFormat('H:i:s', $divisionOut);
                $overtimeStart = Carbon::parse($attendance->check_out)
                    ->setTimeFromTimeString($divisionOutTime->format('H:i:s'))
                    ->addMinutes(30);
                // $overtimeStart = $divisionOutTime->copy()->addMinutes(30); // cutoff 18:30

                \Log::debug("[OT] {$employee->first_name} {$employee->last_name} | {$ot->overtime_date} | Checkout: {$checkOut->format('Y-m-d H:i')} | Cutoff: {$overtimeStart->format('H:i')}");

                if ($checkOut->lte($overtimeStart)) {
                    \Log::debug("[OT] {$employee->first_name} {$employee->last_name} | {$ot->overtime_date} | Checkout {$checkOut->format('H:i')} ≤ 18:30 → 0 jam");
                    continue;
                }

                $overtimeMinutes = $overtimeStart->diffInMinutes($checkOut);
                $hours = ceil($overtimeMinutes / 60);

                // maksimal 2 jam / hari
                $hours = min($hours, 2);

                $totalOvertimeHours += $hours;

                \Log::debug("[OT] {$employee->first_name} {$employee->last_name} | {$ot->overtime_date} | Checkout {$checkOut->format('H:i')} | Overtime {$hours} jam");
            }

            // ⛔ Pastikan overtime minimal 0
            $overtimePay = max(0, $totalOvertimeHours * $hourlyRate);

            \Log::info("[OT-SUMMARY] {$employee->first_name} {$employee->last_name} | Bulan: {$month} | Total OT Hours: {$totalOvertimeHours} | Overtime Pay: {$overtimePay}");


            // --- Hitung gaji dasar & potongan ---
            $totalDeductions = ($totalLateCheckIn * $lateDeduction) + ($totalEarlyCheckOut * $earlyDeduction);
            $baseSalary = $employee->current_salary ?? 0;
            $totalSalary = $baseSalary - $totalDeductions + $overtimePay - $cashAdvance;

            // simpan cash advance lama
            $existing = Payroll::where('employee_id', $employee->employee_id)
                ->where('month', $month)
                ->first();
            $cashAdvance = $existing->cash_advance ?? 0;

            // --- Attendance Allowance ---
            $divisionName = strtolower((string) optional($employee->division)->name);
            if (in_array($divisionName, [
                'supir',
                'kenek',
                'helper',
                'teknisi ac',
                'kasir',
                'admin wholesale',
                'admin retail',
                'admin operasional retail'
            ])) {
                $weeklyData = $this->calculateWeeklyWorkdays($employee, $divisionWorkDays, $month);
                $attendanceAllowance = $employee->attendance_allowance ?? 0;

                if ($attendanceAllowance > 0) {
                    $attendanceDeduction = $this->calculateAttendanceAllowance(
                        $employee,
                        $weeklyData,
                        $attendanceAllowance
                    );
                    $finalAllowance = $attendanceAllowance - $attendanceDeduction;

                    if ($finalAllowance <= 0) {
                        // allowance hangus → potong prorate
                        $maxAbsent = in_array($divisionName, ['kasir', 'admin wholesale', 'admin retail', 'admin operasional retail'])
                            ? 3 : 4;
                        $extraAbsents = max(0, $totalAbsent - $maxAbsent);
                        $prorateDeduction = ($employee->current_salary / 30) * $extraAbsents;

                        $totalSalary -= $prorateDeduction;
                        \Log::info("AttendanceAllowance | {$employee->first_name} {$employee->last_name} | {$divisionName} | Absent: {$totalAbsent} | Hangus → Potong prorate: {$prorateDeduction}");
                    } else {
                        $totalSalary += $finalAllowance;
                        \Log::info("AttendanceAllowance | {$employee->first_name} {$employee->last_name} | {$divisionName} | Allowance Final: {$finalAllowance}");
                    }
                }
            }

            \Log::info("Payroll Permanent Summary | {$employee->first_name} {$employee->last_name} | Month: {$month} | Workdays: {$monthlyWorkdays} | Worked: {$totalDaysWorked} | Absent: {$totalAbsent} | Base: {$baseSalary} | OT Hours: {$totalOvertimeHours} | OT Pay: {$overtimePay} | Total: {$totalSalary}");

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
            \Log::error("Payroll Permanent ERROR | {$employee->first_name} {$employee->last_name} | {$month} | {$e->getMessage()}");
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
                ->filter(fn($d) => in_array($d->format('l'), ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']))
                ->take(2)
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray();
        }

        $workdays = collect($period)->filter(function ($date) use ($effectiveDays, $holidayDates, $employee, $division, $kasirExtraOff) {
            $dayName = $date->format('l');
            $dateStr = $date->format('Y-m-d');

            // Log awal
            // \Log::debug("[CHECK-DIVISION] {$employee->first_name} {$employee->last_name} | Division: {$division} | Day: {$dayName} | Date: {$dateStr}");

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

        $kasirExtraOff = [];
        if ($division === 'kasir') {
            $kasirExtraOff = collect(CarbonPeriod::create($startDate, $endDate))
                ->filter(fn($d) => in_array($d->format('l'), ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']))
                ->take(2)
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray();
        }

        foreach ($period as $date) {
            $dayName = $date->format('l');
            $weekNum = $date->weekOfMonth;
            $dateStr = $date->format('Y-m-d');

            // cek hari kerja
            if (!in_array($dayName, $effectiveDays)) continue;
            if (in_array($dateStr, $holidayDates)) continue;

            // skip 2 weekday off untuk kasir
            if ($division === 'kasir' && in_array($dateStr, $kasirExtraOff)) {
                \Log::debug("[SKIP] Kasir extra weekday off {$dateStr}");
                continue;
            }

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
                    continue;
                }
            }

            // Tentukan hadir/tidak hadir
            $attended = isset($attendances[$dateStr]);
            $weeklyData[$weekNum][$dayName] = $attended;

            \Log::debug(message: "[WeeklyData] {$employee->first_name} {$employee->last_name} | {$dateStr} ({$dayName}) | Week {$weekNum} | Attended: " . ($attended ? 'Yes' : 'No'));
        }

        return $weeklyData;
    }


    private function calculateAttendanceAllowance($employee, $weeklyData, $baseAllowance)
    {

        $division = strtolower((string) optional($employee->division)->name);

        // hitung total absen semua minggu
        $absencesPerWeek = [];
        foreach ($weeklyData as $weekNum => $days) {
            $absencesPerWeek[(int)$weekNum] = count(array_filter($days, fn($attended) => !$attended));
        }
        $totalAbsents = array_sum($absencesPerWeek);

        // 📌 Kasir & Admin pakai aturan langsung per absen
        if (in_array($division, ['kasir', 'admin wholesale', 'admin retail', 'admin operasional retail'])) {
            if ($totalAbsents > 3) {
                // allowance hangus total
                \Log::info("AttendanceAllowance | {$employee->first_name} {$employee->last_name} | {$division} | Absent {$totalAbsents}x > 3 → allowance hangus total.");
                return $baseAllowance; // deduction penuh
            }

            // tiap absen potong langsung per-absen allowance
            $perAbsentDeduction = $baseAllowance / 3;
            $deduction = $perAbsentDeduction * $totalAbsents;

            \Log::info("AttendanceAllowance | {$employee->first_name} {$employee->last_name} | {$division} | Absent {$totalAbsents}x → Deduction {$deduction}");

            return $deduction;
        }

        // 📌 Divisi lain tetap pakai scheme weekly share
        $maxAbsent = 4; // supir/helper/teknisi ac
        if ($totalAbsents > $maxAbsent) {
            \Log::info("AttendanceAllowance | {$employee->first_name} {$employee->last_name} | {$division} | Absent {$totalAbsents}x > {$maxAbsent} → allowance hangus total.");
            return $baseAllowance; // deduction penuh
        }

        // Weekly share scheme
        $scheme = $this->calculateWeeklyShareAllowance($baseAllowance, $absencesPerWeek);
        $shares = $scheme['shares'];
        $allFull = $scheme['allFull'];
        $lostShares = $scheme['lostShares'];
        $lostAllFull = $scheme['lostAllFull'];

        $deduction = 0;
        foreach ($lostShares as $w => $val) {
            $deduction += (float)$val;
        }

        if ($lostAllFull) {
            $allFullOriginal = $baseAllowance - (4 * ($baseAllowance / 6));
            $deduction += $allFullOriginal;
        }

        \Log::info("AttendanceAllowance | {$employee->first_name} {$employee->last_name} | {$division} | Absent {$totalAbsents}x → deduction {$deduction}");

        return $deduction;
    }


    private function calculateWeeklyShareAllowance(float $baseAllowance, array $absencesPerWeek): array
    {
        // definisi share: 4 minggu + allFull (4*(1/6) + 2/6)
        $weekShare = $baseAllowance / 6;
        $shares = [
            1 => $weekShare,
            2 => $weekShare,
            3 => $weekShare,
            4 => $weekShare,
        ];
        $allFull = $baseAllowance - array_sum($shares); // biasanya 2/6

        $lostShares = [];
        $lostAllFull = false;
        $extraAbsencesOnNonShareWeeks = 0;

        // Proses hanya untuk minggu yang relevan (1..4)
        foreach ($absencesPerWeek as $week => $absenceCount) {
            // if ($week < 1 || $week > 4) {
            //     // absen di luar minggu share → hitung untuk prorata
            //     $extraAbsencesOnNonShareWeeks += max(0, (int)$absenceCount);
            //     continue;
            // }

            $absenceCount = max(0, (int)$absenceCount);

            if ($absenceCount >= 1) {
                // minggu ini hangus
                if (isset($shares[$week]) && $shares[$week] > 0) {
                    $lostShares[$week] = $shares[$week];
                    $shares[$week] = 0;
                } else {
                    // jika absen di minggu di luar share (misal week 5) → treat sebagai 1 share hilang
                    // $extraAbsencesOnNonShareWeeks += $absenceCount;
                    $lostShares[$week] = $weekShare;
                }

                $lostAllFull = true; // allFull hangus
            }

            if ($absenceCount >= 2 && $week >= 1 && $week <= 4) {
                // minggu berikutnya hangus juga
                $next = $week + 1;
                if (isset($shares[$next]) && $shares[$next] > 0) {
                    $lostShares[$next] = $shares[$next];
                    $shares[$next] = 0;
                }
            }

            if ($absenceCount >= 3) {
                // semua minggu hangus
                foreach (array_keys($shares) as $w) {
                    if ($shares[$w] > 0) {
                        $lostShares[$w] = $shares[$w];
                        $shares[$w] = 0;
                    }
                }
                $lostAllFull = true;
                break;
            }
        }

        // Total allowance setelah potongan mingguan
        $allowanceAfterShares = array_sum($shares) + ($lostAllFull ? 0 : $allFull);

        // Hitung prorate untuk absen di luar minggu share
        $proratePerAbsence = $baseAllowance / 30;
        $prorateDeduction = $extraAbsencesOnNonShareWeeks * $proratePerAbsence;

        $finalAllowance = max(0, $allowanceAfterShares - $prorateDeduction);

        return [
            'shares' => $shares,
            'allFull' => $lostAllFull ? 0 : $allFull,
            'lostShares' => $lostShares,
            'lostAllFull' => $lostAllFull,
            'extraAbsencesOnNonShareWeeks' => $extraAbsencesOnNonShareWeeks,
            'prorateDeduction' => $prorateDeduction,
            'finalAllowance' => $finalAllowance,
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
}
