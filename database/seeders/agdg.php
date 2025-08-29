<?php

namespace Database\Seeders;

use App\Models\Attandance;
use Illuminate\Database\Seeder;
use App\Models\Employee; 
use App\Models\Attendance; 
use Faker\Factory as Faker;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Buat 15 dummy data karyawan
        for ($i = 1; $i <= 15; $i++) {
            $employee = Employee::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'place_birth' => $faker->city,
                'date_birth' => $faker->date,
                'personal_no' => $faker->unique()->numerify('P-######'),
                'address' => $faker->address,
                'current_address' => $faker->address,
                'blood_type' => $faker->randomElement(['A', 'B', 'AB', 'O']),
                'blood_rhesus' => $faker->randomElement(['+', '-']),
                'phone_number' => $faker->phoneNumber,
                'hp_number' => $faker->phoneNumber,
                'marital_status' => $faker->randomElement(['Married', 'Single','widow','widower']),
                'last_education' => $faker->randomElement(['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3']),
                'degree' => $faker->randomElement(['SMA', 'S1', 'S2']),
                'starting_date' => $faker->date,
                'interview_by' => $faker->name,
                'current_salary' => $faker->numberBetween(3000000, 15000000),
                'insurance' => $faker->boolean,
                'serious_illness' => $faker->randomElement(['None', 'Diabetes', 'Hypertension']),
                'hereditary_disease' => $faker->randomElement(['None', 'Heart Disease']),
                'emergency_contact' => $faker->name,
                'relations' => $faker->randomElement(['Spouse', 'Parent', 'Sibling']),
                'emergency_number' => $faker->phoneNumber,
                'status' => $faker->randomElement(['active', 'inactive']),
            ]);

            // Tambah data absensi untuk karyawan ini
            for ($j = 1; $j <= 5; $j++) { // 5 hari absensi dummy per karyawan
                $checkIn = Carbon::now()->subDays(rand(1, 30))->setTime(rand(7, 9), rand(0, 59));
                $checkOut = (clone $checkIn)->addHours(rand(8, 10)); // Waktu checkout acak antara 8-10 jam setelah checkin

                Attandance::create([
                    'employee_id' => $employee->employee_id, // Menggunakan employee_id dari model Employee
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'check_in_status' => $faker->randomElement(['IN', 'LATE']), // Sesuaikan dengan enum migrasi
                    'check_out_status' => $faker->randomElement(['OUT', 'EARLY']), // Sesuaikan dengan enum migrasi
                    'image' => $faker->imageUrl(640, 480, 'people'), // Gambar dummy
                ]);
            }
        }
    }
}












//USER SEEDER
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Ambil role yang sudah ada
        $adminRole = Role::where('name', 'superadmin')->first(); // Nama role sesuai di seeder sebelumnya
        $managerRole = Role::where('name', 'manager')->first();
        $employeeRole = Role::where('name', 'employee')->first();

        // Menambahkan user Superadmin
        $superadmin = User::updateOrCreate([
            'email' => 'superadmin@gmail.com',
        ], [
            'name' => 'Superadmin',
            'password' => Hash::make('password'),
        ]);

        // Assign role Superadmin ke user
        if ($adminRole) {
            $superadmin->assignRole($adminRole);
        }

        // Menambahkan user Manager
        $manager = User::updateOrCreate([
            'email' => 'manager@gmail.com',
        ], [
            'name' => 'Manager',
            'password' => Hash::make('password'),
        ]);

        // Assign role Manager ke user
        if ($managerRole) {
            $manager->assignRole($managerRole);
        }

        // Menambahkan user Employee
        $employee = User::updateOrCreate([
            'email' => 'employee@gmail.com',
        ], [
            'name' => 'Employee',
            'password' => Hash::make('password'),
        ]);

        // Assign role Employee ke user
        if ($employeeRole) {
            $employee->assignRole($employeeRole);
        }

        // Menambahkan user bunga
        $employee = User::updateOrCreate([
            'email' => 'bungadevtri@gmail.com',
        ], [
            'name' => 'Bunga Putri',
            'password' => Hash::make('password'),
        ]);
        // Assign role Bunga ke user
        if ($managerRole) {
            $manager->assignRole($managerRole);
        }
    }
}







//EMPLOYEE SEEDET
<?php

namespace Database\Seeders;

use App\Models\Attandance;
use App\Models\User;
use App\Models\Employee; // Pastikan ini sesuai dengan nama model Anda
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Buat 15 dummy data karyawan
        for ($i = 1; $i <= 15; $i++) {
            // Buat Employee baru
            $employee = Employee::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'place_birth' => $faker->city,
                'date_birth' => $faker->date,
                'personal_no' => $faker->unique()->numerify('P-######'),
                'address' => $faker->address,
                'current_address' => $faker->address,
                'blood_type' => $faker->randomElement(['A', 'B', 'AB', 'O']),
                'blood_rhesus' => $faker->randomElement(['+', '-']),
                'phone_number' => $faker->phoneNumber,
                'hp_number' => $faker->phoneNumber,
                'marital_status' => $faker->randomElement(['Married', 'Single', 'Widow', 'Widower']),
                'last_education' => $faker->randomElement(['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3']),
                'degree' => $faker->randomElement(['SMA', 'S1', 'S2']),
                'starting_date' => $faker->date,
                'interview_by' => $faker->name,
                'current_salary' => $faker->numberBetween(3000000, 15000000),
                'insurance' => $faker->boolean,
                'serious_illness' => $faker->randomElement(['None', 'Diabetes', 'Hypertension']),
                'hereditary_disease' => $faker->randomElement(['None', 'Heart Disease']),
                'emergency_contact' => $faker->name,
                'relations' => $faker->randomElement(['Spouse', 'Parent', 'Sibling']),
                'emergency_number' => $faker->phoneNumber,
                'status' => $faker->randomElement(['active', 'inactive']),
            ]);

            // Buat User untuk karyawan ini
            User::create([
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $employee->email,
                'password' => Hash::make('defaultpassword'), // Password default
                // Anda dapat menambahkan kolom lain sesuai kebutuhan
            ]);

            // Tambah data absensi untuk karyawan ini
            for ($j = 1; $j <= 5; $j++) { // 5 hari absensi dummy per karyawan
                $checkIn = Carbon::now()->subDays(rand(1, 30))->setTime(rand(7, 9), rand(0, 59));
                $checkOut = (clone $checkIn)->addHours(rand(8, 10)); // Waktu checkout acak antara 8-10 jam setelah checkin

                Attandance::create([
                    'employee_id' => $employee->employee_id, // Menggunakan employee_id dari model Employee
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'check_in_status' => $faker->randomElement(['IN', 'LATE']), // Sesuaikan dengan enum migrasi
                    'check_out_status' => $faker->randomElement(['OUT', 'EARLY']), // Sesuaikan dengan enum migrasi
                    'image' => $faker->imageUrl(640, 480, 'people'), // Gambar dummy
                ]);
            }
        }
    }
}

