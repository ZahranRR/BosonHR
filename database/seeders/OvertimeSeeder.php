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
        $employee = Employee::where('division_id', 11) // misal divisi admin wholesale
            ->where('employee_id', 16)
            ->first();

        if (!$employee) {
            $this->command->warn("No freelance admin wholesale found");
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
            'overtime_date' => Carbon::now()->subDays(1),
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
