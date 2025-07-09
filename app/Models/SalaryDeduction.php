<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'late_deduction', 
        'early_deduction', 
    ];

    protected $casts = [
        'late_deduction' => 'integer',
        'early_deduction' => 'integer',
    ];
}
