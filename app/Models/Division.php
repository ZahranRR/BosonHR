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
        'has_overtime',
        'hourly_rate',
        'work_days',
    ];

    protected $casts = [
        'work_days' => 'array',
        'hourly_rate' => 'integer',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}


