<?php

namespace Database\Seeders;

use App\Models\Attandance;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use Carbon\Carbon;

class FreelanceAttendanceSeederr extends Seeder
{
    public function run()
    {
        // Ambil semua karyawan freelance
        $freelancers = Employee::where('employee_type', 'freelance')->get();

        foreach ($freelancers as $freelancer) {
            $startDate = Carbon::create(2025, 9, 1);
            $endDate = $startDate->copy()->endOfMonth();

            while ($startDate->lte($endDate)) {
                // Kerja hanya Senin–Sabtu
                if (in_array($startDate->format('l'), ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])) {
                    // 60% kemungkinan hadir
                    if (rand(1, 100) <= 90) {
                        // Random jam masuk antara 09:00–09:30
                        $checkInHour = 9;
                        $checkInMinute = rand(0, 30);
                        $checkIn = $startDate->copy()->setTime($checkInHour, $checkInMinute);

                        // Cek apakah sudah ada entri pada tanggal ini untuk karyawan ini
                        $alreadyExists = Attandance::where('employee_id', $freelancer->employee_id)
                            ->whereDate('check_in', $checkIn->toDateString())
                            ->exists();

                        if (!$alreadyExists) {
                            // Random jam keluar: 8–12 jam kerja
                            $checkOut = $checkIn->copy()->addHours(rand(2, 12));

                            Attandance::create([
                                'employee_id' => $freelancer->employee_id,
                                'check_in' => $checkIn,
                                'check_out' => $checkOut,
                                'check_in_status' => 'IN',
                                'check_out_status' => 'OUT',
                                'image' => null,
                            ]);
                        }
                    }
                }

                $startDate->addDay(); // Lanjut ke hari berikutnya
            }
        }
    }
}
