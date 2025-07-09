<?php

namespace App\Http\Controllers\Superadmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role.index')->only('index');
        $this->middleware('permission:role.create')->only(['create', 'store']);
        $this->middleware('permission:role.edit')->only(['edit', 'update']);
        $this->middleware('permission:role.delete')->only('destroy');
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $roles = Role::paginate(10);

        $activeRoles = $roles->filter(function ($role) {
            return $role->isActive(); // Menggunakan method isActive()
        });

        $disabledRoles = $roles->filter(function ($role) {
            return !$role->isActive(); // Menggunakan method isActive()
        });

        return view('Superadmin.MasterData.Role.index', compact('activeRoles', 'disabledRoles', 'roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $rolePermissions = [];
        return view('Superadmin.MasterData.Role.create', compact('permissions', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
            'status' => 'required|string|in:enable,disable',
        ]);
        try {
            $role = Role::create([
                'name' => $validatedData['name'],
                'status' => $validatedData['status'],
            ]);

            $role->syncPermissions($validatedData['permissions']);

            return redirect()->route('role.index')->with('success', 'Role berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('Superadmin.MasterData.Role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
            'status' => 'required|string|in:enable,disable',
        ]);
        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $validatedData['name'],
                'status' => $validatedData['status'],
            ]);

            $role->syncPermissions($validatedData['permissions']);

            return redirect()->route('role.index')->with('success', 'Role berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'superadmin') {
            return redirect()->route('role.index')->with('error', 'Role Superadmin tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus.');
    }
}
