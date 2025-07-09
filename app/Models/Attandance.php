<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attandance extends Model
{
    use HasFactory;

    protected $primaryKey = 'attandance_id';

    protected $fillable = ['employee_id', 'check_in', 'check_out', 'check_in_status', 'check_out_status', 'image'];
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}