<?php

namespace App\Models;

use App\Models\Attandance;
use App\Models\Payroll;
use App\Models\Overtime;
use App\Models\WorkdaySetting;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $primaryKey = 'employee_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['user_id', 'recruitment_id', 'employee_number', 'first_name', 'last_name', 'email', 'check_in_time', 'check_out_time', 'division_id', 'place_birth', 'date_birth', 'identity_number', 'address', 'current_address', 'blood_type', 'blood_rhesus', 'phone_number', 'hp_number', 'marital_status', 'cv_file', 'update_cv', 'last_education', 'degree', 'starting_date', 'interview_by', 'current_salary', 'attendance_allowance', 'insurance', 'serious_illness', 'hereditary_disease', 'emergency_contact', 'relations', 'emergency_number', 'status', 'employee_type'];

    protected $hidden = [];

    protected $casts = [
        'insurance' => 'boolean',
        'current_salary' => 'integer',
        'update_cv' => 'datetime',
    ];

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id'); // Menggunakan kolom recruitment_id
    }

    // Relasi dengan Attendance

    public function attendances(): HasMany
    {
        return $this->hasMany(Attandance::class, 'employee_id', 'employee_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(Attandance::class, 'employee_id', 'employee_id');
    }


    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'employee_id', 'employee_id');
    }

    public function attandanceRecap()
    {
        return $this->hasMany(AttandanceRecap::class, 'employee_id', 'employee_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email'); // Menghubungkan berdasarkan email
    }

    public function offrequests()
    {
        return $this->hasMany(Offrequest::class, 'user_id', 'user_id');
    }

    // Relasi dengan WorkdaySetting (Jika ada)
    public function workdaySetting(): HasOne
    {
        return $this->hasOne(WorkdaySetting::class, 'employee_id', 'employee_id');
    }

    // Relasi dengan Overtime
    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class, 'employee_id', 'employee_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
