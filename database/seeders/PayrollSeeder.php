<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AttandanceRecap;
use App\Models\Offrequest;
use App\Models\WorkdaySetting;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class PayrollSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua data karyawan
        $employees = User::all();

        // Ambil data workday_setting
        $workdaySetting = WorkdaySetting::first();
        $monthlyWorkdays = $workdaySetting ? $workdaySetting->monthly_workdays : 0;


        // Loop untuk setiap employee


            // Ambil data dari AttandanceRecap untuk karyawan ini
            $attendanceRecap = AttandanceRecap::where('employee_id', $employee->id)
                                               ->where('month', now()->format('Y-m')) // Ambil bulan ini
                                               ->first();

            // Jika tidak ada data kehadiran, set default 0
            $totalPresent = $attendanceRecap ? $attendanceRecap->total_present : 0;
            $totalLate = $attendanceRecap ? $attendanceRecap->total_late : 0;
            $totalEarly = $attendanceRecap ? $attendanceRecap->total_early : 0;

            // Ambil total days off dari offrequest yang statusnya approved
            $totalDaysOff = Offrequest::where('user_id', $employee->id)
                                      ->where('status', 'approved')
                                      ->get()
                                      ->sum(function ($offrequest) {
                                          return $offrequest->start_event->diffInDays($offrequest->end_event) + 1;
                                      });

            // Hitung total hari yang bekerja (perbedaan antara hari kerja dan hari libur)
            $totalWorkedDays = $monthlyWorkdays - $totalDaysOff;

            // Hitung gaji berdasarkan kehadiran (asumsi dasar untuk contoh ini)
            $salaryPerDay = $employee->current_salary / $monthlyWorkdays;
            $totalSalary = $totalWorkedDays * $salaryPerDay;

            // Membuat data payroll untuk employee ini
            DB::table('payrolls')->insert([
                'employee_id' => $employee->id,
                'salary' => $totalSalary,
                'total_days_worked' => $totalWorkedDays,
                'total_days_off' => $totalDaysOff,
                'total_late_check_in' => $totalLate,
                'total_early_check_out' => $totalEarly,
                'month' => now()->format('F Y'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Menampilkan pesan berhasil
        $this->command->info('Payroll seeder berhasil dijalankan!');
    }
}
