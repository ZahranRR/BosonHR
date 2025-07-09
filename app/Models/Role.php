<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Validation\ValidationException;
class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = ['name', 'guard_name', 'status'];

    protected static function booted()
    {
        static::deleting(function ($role) {
            if ($role->name === 'superadmin') {
                throw ValidationException::withMessages(['error' => 'Role Superadmin tidak dapat dihapus.']);
            }
        });
    }

    // Scope untuk status aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'enable');
    }

    // Scope untuk status non-aktif
    public function scopeInactive($query)
    {
        return $query->where('status', 'disable');
    }

    // Cek jika role aktif
    public function isActive(): bool
    {
        return $this->status === 'enable';
    }
}
