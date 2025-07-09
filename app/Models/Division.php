<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'description',
        'work_start_time',
        'work_end_time',
        'has_overtime',
        'work_days',
    ];

    protected $casts = [
        'work_days' => 'array',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}


