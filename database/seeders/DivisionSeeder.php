<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Cache\NullStore;

class DivisionSeeder extends Seeder
{
    public function run()
    {
        
        DB::table('divisions')->insert([
            [
                'id'          => 1,
                'name'        => 'Teknisi AC',
                'description' => 'Teknisi AC',
                'work_days'   => json_encode(["Monday","Tuesday","Wednesday","Thursday,Friday,Saturday,Sunday"]),
                'has_overtime'=> 0,
                'hourly_rate' => null,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'id'          => 2,
                'name'        => 'Freenlance Admin',
                'description' => 'Freelance Admin SMD',
                'work_days'   => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]),
                'has_overtime'=> 1,
                'hourly_rate' => 10000,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'id'          => 3,
                'name'        => 'Supir',
                'description' => 'Supir/Driver',
                'work_days'   => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'=> 0,
                'hourly_rate' => null,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'id'          => 4,
                'name'        => 'Helper',
                'description' => 'Helper',
                'work_days'   => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'=> 0,
                'hourly_rate' => null,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'id'          => 5,
                'name'        => 'Kenek',
                'description' => "Driver's Assistant",
                'work_days'   => json_encode(["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]),
                'has_overtime'=> 0,
                'hourly_rate' => null,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ]);
    }
}
