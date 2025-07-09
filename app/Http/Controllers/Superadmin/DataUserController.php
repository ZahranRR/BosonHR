<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Events\UserCreated; // Import Event
use Illuminate\Support\Str;

class DataUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user.index')->only(['index']);
        $this->middleware('permission:user.create')->only(['create', 'store']);
        $this->middleware('permission:user.edit')->only(['edit', 'update']);
        $this->middleware('permission:user.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        if ($users->isEmpty() && $search) {
            return redirect()
                ->route('datauser.index')
                ->withErrors(['No data found for the search term: ' . $search]);
        }

        return view('Superadmin.MasterData.user.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('Superadmin.MasterData.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Membuat user baru
            $passwordPlain = $request->input('password'); // Simpan password plain untuk email
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($passwordPlain),
            ]);

            // Menetapkan role untuk user
            $user->assignRole($request->input('role'));

            // Trigger event untuk mengirimkan email
            event(new UserCreated($user, $passwordPlain));

            // Redirect kembali dengan session success
            return redirect()->route('datauser.index')->with('success', 'Akun berhasil dibuat dan email notifikasi telah dikirim.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function edit($userId)
    {
        $user = User::findOrFail($userId); // Temukan user berdasarkan user_id atau tampilkan 404 jika tidak ditemukan

        $roles = Role::all(); // Ambil semua role yang tersedia
        $isEmployee = $user->employee()->exists(); // Cek apakah user terkait dengan employee

        return view('Superadmin.MasterData.user.update', [
            'user' => $user,
            'roles' => $roles,
            'isEmployee' => $isEmployee,
        ]);
    }

    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'role' => 'required|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        try {
            // Cek apakah user memiliki hubungan dengan employee
            $isEmployee = $user->employee()->exists();

            if ($isEmployee) {
                // Jika user terkait dengan employee, tidak perbolehkan update name dan email
                $user->syncRoles($validatedData['role']);

                // Update password jika ada
                if (!empty($validatedData['password'])) {
                    $user->password = Hash::make($validatedData['password']);
                }
            } else {
                // Jika user tidak terkait dengan employee, perbolehkan update name dan email
                $user->name = $validatedData['name'];
                $user->email = $validatedData['email'];

                // Update password jika ada
                if (!empty($validatedData['password'])) {
                    $user->password = Hash::make($validatedData['password']);
                }

                // Sinkronisasi role
                $user->syncRoles($validatedData['role']);
            }

            // Simpan perubahan
            $user->save();

            return redirect()->route('datauser.index')->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId); // Perbaikan: Menggunakan findOrFail

        $user->delete();
        return redirect()->route('datauser.index')->with('success', 'Data berhasil dihapus!');
    }
}
