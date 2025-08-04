<?php
namespace App\Helpers;

use Carbon\CarbonPeriod;

class AttendanceHelper
{
    public static function calculateWorkedDays($startDate, $endDate, $workdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])
    {
        // Generate days between start and end date
        $period = CarbonPeriod::create($startDate, $endDate);
        $workedDays = 0;

        foreach ($period as $date) {
            // Check if the day is a workday (not a weekend)
            if (in_array($date->format('l'), $workdays)) {
                $workedDays++;
            }
        }

        return $workedDays;
    }
}
