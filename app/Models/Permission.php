<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;


class Permission extends SpatiePermission
{
    use HasFactory;
    protected $fillable = ['name', 'role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
