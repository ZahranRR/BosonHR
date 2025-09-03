<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // id 5
            [
                'id'            => 5,
                'name'          => 'Freenlance', // sesuai dump
                'description'   => 'Freelance Admin',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]),
                'has_overtime'  => 1,
                'hourly_rate'   => 10000,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 6
            [
                'id'            => 6,
                'name'          => 'Supir',
                'description'   => 'Supir/Driver',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'  => 0,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 7
            [
                'id'            => 7,
                'name'          => 'Helper',
                'description'   => 'Helper',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'  => 0,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 8
            [
                'id'            => 8,
                'name'          => "Kenek",
                'description'   => "Driver's Assistant",
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'  => 0,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 10
            [
                'id'            => 10,
                'name'          => 'Teknisi AC',
                'description'   => 'Technician',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'  => 0,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 11
            [
                'id'            => 11,
                'name'          => 'Admin Wholesale',
                'description'   => 'Admin Wholesale',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]),
                'has_overtime'  => 1,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 12
            [
                'id'            => 12,
                'name'          => 'Admin Retail',
                'description'   => 'Admin Retail',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]),
                'has_overtime'  => 0,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '17:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 13
            [
                'id'            => 13,
                'name'          => 'Admin Operasional Retail',
                'description'   => 'Admin Operasional Retail',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]),
                'has_overtime'  => 0,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            // id 14
            [
                'id'            => 14,
                'name'          => 'Kasir',
                'description'   => 'Cashier',
                'work_days'     => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'  => 0,
                'hourly_rate'   => null,
                'check_in_time' => '09:00:00',
                'check_out_time'=> '18:00:00',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        // Upsert agar aman jika ID sudah ada
        DB::table('divisions')->upsert(
            $rows,
            ['id'], // unique/constraint
            ['name','description','work_days','has_overtime','hourly_rate','check_in_time','check_out_time','updated_at']
        );
    }
}
