<?php

namespace Database\Seeders;

use App\Models\Attandance;
use App\Models\AttandanceRecap;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\AttendanceRecap;
use App\Models\WorkdaySetting;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        // Ambil data WorkdaySetting
        $workdaySetting = WorkdaySetting::first();
        $monthlyWorkdays = $workdaySetting ? $workdaySetting->monthly_workdays : 0; // Ambil jumlah hari kerja bulanan

        // Mendapatkan semua ID karyawan
        $employeeIds = range(1, 16); // Sesuaikan dengan jumlah karyawan yang ada di tabel employees

        foreach ($employeeIds as $employeeId) {
            // Inisialisasi variabel untuk setiap karyawan
            $totalPresent = 0; // Total hadir
            $totalLate = 0;    // Total terlambat
            $totalEarly = 0;   // Total pulang awal

            // Simulasi data kehadiran untuk setiap hari selama bulan ini
            for ($day = 1; $day <= 20; $day++) {
                $checkInTime = Carbon::create(2024, 11, $day, rand(8, 9), rand(0, 59)); // Jam check-in acak antara jam 8 dan 9
                $checkOutTime = $checkInTime->copy()->addHours(8); // Jam check-out 8 jam setelah check-in

                // Simulasi apakah telat atau pulang awal
                $isLate = rand(0, 1); // Acak apakah telat atau tidak
                $isEarly = rand(0, 1); // Acak apakah awal pulang atau tidak

                // Jika telat, hitung sebagai total late dan total present
                if ($isLate) {
                    $totalLate++;
                }

                // Jika awal pulang, hitung sebagai total early
                if ($isEarly) {
                    $totalEarly++;
                }

                // Hitung total hadir (termasuk yang telat dan yang tepat waktu)
                $totalPresent++; // Setiap kehadiran, baik terlambat atau tepat waktu, dihitung sebagai hadir

                // Membuat data kehadiran untuk setiap hari
                Attandance::create([
                    'employee_id' => $employeeId,
                    'check_in' => $checkInTime,
                    'check_out' => $checkOutTime,
                    'check_in_status' => 'IN',
                    'check_out_status' => 'OUT',
                    'image' => null, // Ganti dengan path gambar jika ada
                ]);
            }

            // Menghitung total absen dengan mengurangi total hadir dari hari kerja bulanan
            $totalAbsent = $monthlyWorkdays - $totalPresent;

            // Cek apakah totalAbsent kurang dari 0 (jika ada kesalahan perhitungan)
            if ($totalAbsent < 0) {
                $totalAbsent = 0;
            }

            // Membuat data recap kehadiran bulanan
            AttandanceRecap::create([
                'employee_id' => $employeeId,
                'month' => Carbon::now()->format('Y-m'), 
                'total_present' => $totalPresent,
                'total_late' => $totalLate,
                'total_early' => $totalEarly,
                'total_absent' => $totalAbsent,
            ]);
        }
    }

}
