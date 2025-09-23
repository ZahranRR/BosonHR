<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Attandance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AllInOneAttendanceSeeder extends Seeder
{
    public function run()
    {
        $month = '2025-09';
        [$year, $monthNumber] = explode('-', $month);

        $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $monthNumber, 1)->endOfMonth();
        $period    = CarbonPeriod::create($startDate, $endDate);

        $divisions = [
            'Admin Wholesale' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
            'Admin Retail' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'check_in' => '09:00',
                'check_out' => '17:00',
            ],
            'Admin Operasional Retail' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
            'Kasir' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
            'Supir' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
            'Kenek' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
            'Helper' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
            'Teknisi AC' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
            'freelance admin' => [
                'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'check_in' => '09:00',
                'check_out' => '18:00',
            ],
        ];

        foreach ($divisions as $divisionName => $config) {
            $division = Division::where('name', $divisionName)->first();
            if (!$division) continue;

            $employees = Employee::where('division_id', $division->id)->get();
            if ($employees->isEmpty()) continue;

            // --- Global absent randomizer (per divisi, tapi sama untuk semua employee di divisi itu) ---
            $workDates = [];
            foreach ($period as $date) {
                if (in_array($date->format('l'), $config['days'])) {
                    $workDates[] = $date->toDateString();
                }
            }

            // Tentukan berapa hari absen random untuk divisi ini (0–6 hari)
            $absentCount = rand(0, 6);
            $absentDays = [];
            if ($absentCount > 0 && count($workDates) >= $absentCount) {
                $absentKeys = (array) array_rand($workDates, $absentCount);
                $absentDays = array_map(fn($key) => $workDates[$key], $absentKeys);
            }

            // --- Generate attendance untuk semua karyawan di divisi ---
            foreach ($employees as $employee) {
                foreach ($workDates as $dateStr) {
                    if (in_array($dateStr, $absentDays)) {
                        continue; // ❌ skip → dianggap absen
                    }

                    $date = Carbon::parse($dateStr);

                    // Jam masuk 09:00–09:30
                    $checkInHour = 9;
                    $checkInMinute = rand(0, 30);
                    $checkIn = $date->copy()->setTime($checkInHour, $checkInMinute);

                    // Jam keluar sesuai divisi + extra 0–120 menit
                    $baseOut = Carbon::parse($date->format('Y-m-d') . ' ' . $config['check_out']);
                    $checkOut = $baseOut->copy()->addMinutes(rand(0, 120));

                    $checkInStatus = ($checkInMinute > 15) ? 'LATE' : 'IN';
                    $checkOutStatus = ($checkOut->lt($baseOut)) ? 'EARLY' : 'OUT';

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
