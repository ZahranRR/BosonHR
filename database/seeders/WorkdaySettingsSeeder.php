<?php
// database/seeders/WorkdaySettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkdaySetting;

class WorkdaySettingsSeeder extends Seeder
{
    public function run()
    {
        // Tentukan hari kerja efektif default
        $effectiveDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        // Hitung jumlah hari kerja efektif per bulan (asumsi 4 minggu per bulan)
        $weeklyWorkdays = count($effectiveDays);
        $monthlyWorkdays = $weeklyWorkdays * 4;

       // Cek apakah data sudah ada sebelum membuat
       WorkdaySetting::updateOrCreate(
        ['id' => 1], // ID unik agar hanya ada satu pengaturan
        [
            'effective_days' => $effectiveDays,
            'monthly_workdays' => $monthlyWorkdays,
        ]
    );
    }
}
