<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{
    use HasFactory;

    protected $primaryKey = 'cash_advance_id';

    protected $fillable = [
        'employee_id',
        'total_amount',
        'installments',
        'installment_amount',
        'last_processed_month',
        'remaining_installments',
        'start_month',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'division_id');
    }
}
