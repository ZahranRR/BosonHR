<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Overtime;
use App\Models\Employee;
use Carbon\Carbon;

class OvertimeSeeder extends Seeder
{
    public function run()
    {
        //admin wholesale = 11, freelance admin = 5
        $employee = Employee::where('division_id', 5) // misal divisi admin wholesale
            ->where('employee_id', 25)
            ->first();

        if (!$employee) {
            $this->command->warn("No freelance or admin wholesale found");
            return;
        }

        // // Approved overtime
        // Overtime::create([
        //     'employee_id' => $employee->employee_id,
        //     'overtime_date' => Carbon::now()->subDays(3),
        //     'duration' => 2,
        //     'notes' => 'Approved overtime test',
        //     'manager_id' => 2, // sesuaikan dengan manager user_id
        //     'status' => 'approved',
        // ]);

        // Pending overtime
        Overtime::create([
            'employee_id' => $employee->employee_id,
            'overtime_date' => Carbon::now()->addDays(1),
            // 'overtime_date' => Carbon::now()->subDays(1),
            'duration' => 2,
            'notes' => 'Pending overtime test',
            'manager_id' => 2,
            'status' => 'pending',
        ]);

        // Rejected overtime
        // Overtime::create([
        //     'employee_id' => $employee->employee_id,
        //     'overtime_date' => Carbon::now()->subDays(1),
        //     'duration' => 1,
        //     'notes' => 'Rejected overtime test',
        //     'manager_id' => 2,
        //     'status' => 'rejected',
        // ]);
    }
}
