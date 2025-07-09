<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;

    protected $table = 'recruitments';

    protected $primaryKey = 'recruitment_id';
    public $incrementing = true;

    // Menambahkan 'apply_position' ke dalam $fillable
    protected $fillable = ['employee_id', 'user_id', 'first_name', 'last_name', 'email', 'phone_number', 'date_of_birth', 'last_education', 'last_position', 'apply_position', 'cv_file', 'remarks', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'recruitment_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'recruitment_id', 'recruitment_id');
    }
}
