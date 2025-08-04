<?php

namespace App\Observers;

use App\Models\Attandance;
use App\Models\AttandanceRecap;
use App\Models\WorkdaySetting;
use App\Models\Event;
use Carbon\Carbon;

class AttandanceObserver
{
    public function created(Attandance $attendance)
    {
        $this->updateRecap($attendance);
    }

    public function updated(Attandance $attendance)
    {
        $this->updateRecap($attendance);
    }

    private function updateRecap(Attandance $attendance)
    {
        $employeeId = $attendance->employee_id;
        $month = $attendance->created_at->format('Y-m');

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::now();

        // Ambil pengaturan hari kerja
        $workdaySetting = WorkdaySetting::first();
        if (!$workdaySetting) {
            return; // Jika tidak ada pengaturan hari kerja, keluar
        }
        $workdays = $workdaySetting->effective_days;

        // Ambil hari libur dari event
        $holidays = [];
        $dangerEvents = Event::whereBetween('start_date', [$startDate, $endDate])
            ->where('category', 'danger')
            ->get();

        foreach ($dangerEvents as $event) {
            $rangeStart = Carbon::parse($event->start_date);
            $rangeEnd = Carbon::parse($event->end_date);

            while ($rangeStart <= $rangeEnd) {
                $holidays[] = $rangeStart->toDateString();
                $rangeStart->addDay();
            }
        }

        // Hitung total hari kerja efektif
        $totalEffectiveWorkdays = 0;
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            if (in_array($date->format('l'), $workdays) && !in_array($date->toDateString(), $holidays)) {
                $totalEffectiveWorkdays++;
            }
        }

        $attendances = Attandance::where('employee_id', $employeeId)
            ->whereYear('created_at', $attendance->created_at->year)
            ->whereMonth('created_at', $attendance->created_at->month)
            ->get();

        $totalPresent = 0;
        $totalLate = 0;
        $totalEarly = 0;

        foreach ($attendances as $record) {
            if ($record->check_in && $record->check_out) {
                $totalPresent++;
                if ($record->check_in_status === 'LATE') {
                    $totalLate++;
                }
                if ($record->check_out_status === 'EARLY') {
                    $totalEarly++;
                }
            }
        }

        // Hitung total absent
        $totalAbsent = max(0, $totalEffectiveWorkdays - $totalPresent);

        // Simpan atau perbarui data recap
        AttandanceRecap::updateOrCreate(
            ['employee_id' => $employeeId, 'month' => $month],
            [
                'total_present' => $totalPresent,
                'total_late' => $totalLate,
                'total_early' => $totalEarly,
                'total_absent' => $totalAbsent,
            ]
        );
    }
}
