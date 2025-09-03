<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Attandance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AdminAndKasirAttendanceSeeder extends Seeder
{
    public function run()
    {
        $month = '2025-09'; // bulan target
        [$year, $monthNumber] = explode('-', $month);

        $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $monthNumber, 1)->endOfMonth();
        $period    = CarbonPeriod::create($startDate, $endDate);

        $divisions = [
            'Admin Wholesale' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
                'absent_count' => 2, // misal 2x absen random
            ],
            'Admin Retail' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'check_in' => '09:00',
                'check_out' => '17:00',
                'absent_count' => 1,
            ],
            'Admin Operasional Retail' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
                'absent_count' => 3,
            ],
            'Kasir' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
                'absent_count' => 4, // + 2 weekday libur fix
            ],
        ];

        foreach ($divisions as $divisionName => $config) {
            $division = Division::where('name', $divisionName)->first();
            if (!$division) continue;

            $employees = Employee::where('division_id', $division->id)->get();

            foreach ($employees as $employee) {
                // --- Kasir: libur 2 weekday random ---
                $kasirExtraOff = [];
                if ($divisionName === 'Kasir') {
                    $weekdays = collect(CarbonPeriod::create($startDate, $endDate))
                        ->filter(fn($d) => in_array($d->format('l'), ['Monday','Tuesday','Wednesday','Thursday','Friday']))
                        ->map(fn($d) => $d->format('Y-m-d'))
                        ->toArray();
                    $kasirExtraOff = (array) array_rand(array_flip($weekdays), 2);
                }

                // --- Generate workdays ---
                $workDates = [];
                foreach ($period as $date) {
                    $dayName = $date->format('l');
                    $dateStr = $date->toDateString();

                    if (!in_array($dayName, $config['days'])) continue;

                    // Kasir → skip extra off
                    if ($divisionName === 'Kasir' && in_array($dateStr, $kasirExtraOff)) continue;

                    $workDates[] = $dateStr;
                }

                // --- Tentukan absen random ---
                $absentDays = [];
                if ($config['absent_count'] > 0 && count($workDates) >= $config['absent_count']) {
                    $absentKeys = (array) array_rand($workDates, $config['absent_count']);
                    $absentDays = array_map(fn($key) => $workDates[$key], $absentKeys);
                }

                foreach ($workDates as $dateStr) {
                    if (in_array($dateStr, $absentDays)) {
                        // ❌ Absen = tidak ada record sama sekali
                        continue;
                    }

                    $date = Carbon::parse($dateStr);

                    // Jam masuk 09:00–09:30
                    $checkInHour = 9;
                    $checkInMinute = rand(0, 30);
                    $checkIn = $date->copy()->setTime($checkInHour, $checkInMinute);

                    // Jam keluar
                    $baseOut = Carbon::parse($date->format('Y-m-d') . ' ' . $config['check_out']);
                    $checkOut = $baseOut->copy()->addMinutes(rand(0, 120));

                    $checkInStatus = ($checkInMinute > 15) ? 'LATE' : 'IN';
                    $checkOutStatus = ($checkOut->lt($baseOut)) ? 'EARLY' : 'OUT';

                    // Cek kalau sudah ada
                    $alreadyExists = Attandance::where('employee_id', $employee->employee_id)
                        ->whereDate('check_in', $dateStr)
                        ->exists();

                    if (!$alreadyExists) {
                        Attandance::create([
                            'employee_id'      => $employee->employee_id,
                            'check_in'         => $checkIn,
                            'check_out'        => $checkOut,
                            'check_in_status'  => $checkInStatus,
                            'check_out_status' => $checkOutStatus,
                            'image'            => null,
                        ]);
                    }
                }
            }
        }
    }
}
