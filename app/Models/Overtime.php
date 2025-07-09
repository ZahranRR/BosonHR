<?php

// app/Models/Overtime.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'overtime_date', 'duration', 'notes', 'status','manager_id'];


    // Model Overtime
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'user_id');
    }
}
