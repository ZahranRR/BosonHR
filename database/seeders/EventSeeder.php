<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $startYear = date('Y') - 100;
        $endYear = date('Y') + 100;

        for ($year = $startYear; $year <= $endYear; $year++) {
            $startDate = "$year-01-01"; // Awal tahun
            $endDate = "$year-12-31"; // Akhir tahun

            $currentDate = Carbon::createFromFormat('Y-m-d', $startDate);

            while ($currentDate->format('Y-m-d') <= $endDate) {
                // Memeriksa apakah hari ini adalah hari Minggu
                // if ($currentDate->dayOfWeek === 0) {
                //     // Memeriksa apakah event sudah ada untuk tanggal ini
                //     $existingEvent = Event::where('start_date', $currentDate->format('Y-m-d'))
                //         ->where('title', 'holiday')
                //         ->first();


                //     if (!$existingEvent) {
                //         Event::create([
                //             'title' => 'holiday',
                //             'start_date' => $currentDate->format('Y-m-d'),
                //             'end_date' => $currentDate->format('Y-m-d'),
                //             'category' => 'danger', // Kategori 'danger'
                //             'created_at' => now(),
                //             'updated_at' => now(),
                //         ]);
                //     }
                // }

                // Mengupdate tanggal ke hari berikutnya
                $currentDate->addDay();
            }
        }
    }
}
