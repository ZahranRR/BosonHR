<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Attandance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RandomAttendanceSeeder extends Seeder
{

    public function run()
    {
        // Ambil semua karyawan supir & teknisi AC
        // $employees = Employee::join('divisions', 'employees.division_id', '=', 'divisions.id')
        //     ->whereIn('divisions.name', ['Supir', 'Teknisi AC'])
        //     ->get(['employees.employee_id']);

        // contoh hanya untuk 1 employee id = 12
        $employees = Employee::where('employee_id', 10)->get(['employee_id']);

        $month = '2025-08'; // bulan target
        [$year, $monthNumber] = explode('-', $month);

        $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $monthNumber, 1)->endOfMonth();

        foreach ($employees as $employee) {
            $period = CarbonPeriod::create($startDate, $endDate);

            // Buat list semua tanggal kerja (skip Minggu genap → libur)
            $workDates = [];
            foreach ($period as $date) {
                $dayName = $date->format('l');
                $weekNum = $date->weekOfMonth; // minggu ke-n dalam bulan
                $dateStr = $date->toDateString();

                // Minggu genap (2,4) = libur
                if ($dayName === 'Sunday' && $weekNum % 2 === 0) {
                    continue;
                }

                $workDates[] = $dateStr;
            }

            // Tentukan berapa hari random absen
            $absentCount = 3; // contoh, bisa diubah sesuai kebutuhan

            // Ambil N hari random absen
            $absentDays = [];
            if ($absentCount > 0 && count($workDates) >= $absentCount) {
                $absentKeys = (array) array_rand($workDates, $absentCount);
                $absentDays = array_map(fn($key) => $workDates[$key], $absentKeys);
            }

            // Generate attendance
            foreach ($workDates as $dateStr) {
                // Skip jika hari ini adalah absen random
                if (in_array($dateStr, $absentDays)) {
                    continue;
                }

                $date = Carbon::parse($dateStr);

                // Jam masuk 09:00–09:30
                $checkInHour = 9;
                $checkInMinute = rand(0, 30);
                $checkIn = $date->copy()->setTime($checkInHour, $checkInMinute);

                // Jam keluar 18:00–20:00
                $checkOut = $checkIn->copy()->setTime(18, 0)->addMinutes(rand(0, 120));

                // Status check-in
                $checkInStatus = ($checkInMinute > 15) ? 'LATE' : 'IN';
                // Status check-out
                $checkOutStatus = ($checkOut->lt($checkIn->copy()->setTime(18, 0))) ? 'EARLY' : 'OUT';

                // Cek kalau belum ada absen
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

    // public function run()
    // {
    //     // Ambil semua karyawan yang division_name = 'Helper'
    //     // $helpers = Employee::join('divisions', 'employees.division_id', '=', 'divisions.id')
    //     //     ->where('divisions.name', 'Helper')
    //     //     ->get(['employees.employee_id']); 

    //     $helpers = Employee::where('employee_id', 11)->get(['employee_id']);

    //     $month = '2025-08'; // bulan target
    //     [$year, $monthNumber] = explode('-', $month);

    //     $startDate = Carbon::create($year, $monthNumber, 1)->startOfMonth();
    //     $endDate   = Carbon::create($year, $monthNumber, 1)->endOfMonth();

    //     // Tentukan minggu terakhir (hari Minggu terakhir dalam bulan itu → libur fix)
    //     $lastSunday = Carbon::create($year, $monthNumber, 1)
    //         ->endOfMonth()
    //         ->previous('Sunday')
    //         ->toDateString();

    //     foreach ($helpers as $helper) {
    //         $period = CarbonPeriod::create($startDate, $endDate);

    //         // Buat list semua tanggal kerja (kecuali libur Minggu terakhir)
    //         $workDates = [];
    //         foreach ($period as $date) {
    //             $dateStr = $date->toDateString();
    //             if ($dateStr !== $lastSunday) {
    //                 $workDates[] = $dateStr;
    //             }
    //         }

    //         // Tentukan berapa hari random absen
    //         $absentCount = 2; // << bisa kamu ubah jadi 1, 2, 3, dst

    //         // Ambil N hari random absen
    //         $absentDays = [];
    //         if ($absentCount > 0 && count($workDates) >= $absentCount) {
    //             $absentKeys = (array) array_rand($workDates, $absentCount);
    //             $absentDays = array_map(fn($key) => $workDates[$key], $absentKeys);
    //         }

    //         // Generate attendance
    //         foreach ($workDates as $dateStr) {
    //             // Skip jika hari ini adalah absen random
    //             if (in_array($dateStr, $absentDays)) {
    //                 continue;
    //             }

    //             $date = Carbon::parse($dateStr);

    //             // Jam masuk 09:00–09:30
    //             $checkInHour = 9;
    //             $checkInMinute = rand(0, 30);
    //             $checkIn = $date->copy()->setTime($checkInHour, $checkInMinute);

    //             // Jam keluar 18:00–20:00
    //             $checkOut = $checkIn->copy()->setTime(18, 0)->addMinutes(rand(0, 120));

    //             // Status check-in
    //             $checkInStatus = ($checkInMinute > 15) ? 'LATE' : 'IN';
    //             // Status check-out
    //             $checkOutStatus = ($checkOut->lt($checkIn->copy()->setTime(18, 0))) ? 'EARLY' : 'OUT';

    //             // Cek kalau belum ada absen
    //             $alreadyExists = Attandance::where('employee_id', $helper->employee_id)
    //                 ->whereDate('check_in', $dateStr)
    //                 ->exists();

    //             if (!$alreadyExists) {
    //                 Attandance::create([
    //                     'employee_id'      => $helper->employee_id,
    //                     'check_in'         => $checkIn,
    //                     'check_out'        => $checkOut,
    //                     'check_in_status'  => $checkInStatus,
    //                     'check_out_status' => $checkOutStatus,
    //                     'image'            => null,
    //                 ]);
    //             }
    //         }
    //     }
    // }
}
