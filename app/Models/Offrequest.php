<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Offrequest extends Model
{
    protected $table = 'offrequests';
    protected $primaryKey = 'offrequest_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'email', 'title', 'description', 'start_event', 'end_event', 'user_id', 'manager_id', 'status','approved_by', 'approver_id', 'image'];

    protected $casts = [
        'start_event' => 'datetime',
        'end_event' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Menyaring offrequest yang masih pending
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Menyaring offrequest berdasarkan manager
    public function scopeForManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    public function scopeIsOnLeave(Builder $query, $employeeId, $date): Builder
    {
        return $query->where('user_id', $employeeId)->where('status', 'approved')->whereDate('start_event', '<=', $date)->whereDate('end_event', '>=', $date);
    }
}
