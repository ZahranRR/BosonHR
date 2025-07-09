<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    protected $primaryKey = 'user_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $hidden = ['password'];

    protected $fillable = ['user_id','name', 'email', 'password', 'employee_id', 'recruitment_id'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function offrequests()
    {
        return $this->hasMany(Offrequest::class, 'user_id');
    }
    public function offrequestsAsManager()
    {
        return $this->hasMany(Offrequest::class, 'manager_id');
    }

    public function offrequestsAsEmployee()
    {
        return $this->hasMany(Offrequest::class, 'user_id');
    }

    public function approvedOffrequests()
    {
        return $this->hasMany(Offrequest::class, 'approver_id');
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class, 'user_id', 'user_id');
    }

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id', 'recruitment_id');
    }
}
