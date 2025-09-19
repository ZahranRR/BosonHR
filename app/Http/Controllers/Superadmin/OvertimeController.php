<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OvertimeController extends Controller
{
    public function __construct()
    {
        // Menggunakan middleware untuk validasi hak akses
        $this->middleware('permission:overtime.create')->only(['index', 'create', 'store', 'searchEmployees']);
        $this->middleware('permission:overtime.approvals')->only(['approvals', 'reject', 'approve']);
    }

    public function index()
    {
        // Mengambil data overtime untuk employee yang sedang login
        $overtimes = Overtime::whereHas('employee', function ($query) {
            $query->where('employee_id', auth()->user()->employee->employee_id); // Sesuaikan dengan 'employee_id' atau 'id' sesuai data Anda
        })->get();

        return view('Superadmin.overtime.index', compact('overtimes'));

        // // Jika user punya role superadmin, tampilkan semua
        // if (auth()->user()->hasRole('superadmin')) {
        //     $overtimes = Overtime::with('employee')->get();
        // } else {
        //     // Kalau bukan superadmin, hanya tampilkan overtime milik employee yg login
        //     $overtimes = Overtime::whereHas('employee', function ($query) {
        //         $query->where('employee_id', auth()->user()->employee->employee_id);
        //     })->get();
        // }

        // return view('Superadmin.overtime.index', compact('overtimes'));
    }


    public function create()
    {
        // Ambil daftar manager (user dengan role 'manager')
        $managers = User::role('manager')->get();

        // dd($managers);
        return view('Superadmin.overtime.create', compact('managers'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi data overtime
            $request->validate([
                'overtime_date' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        // Validasi apakah sudah ada overtime pada tanggal yang sama
                        $existingOvertime = Overtime::whereHas('employee', function ($query) {
                            $query->where('employee_id', Auth::user()->employee->employee_id);  // Menggunakan employee_id dari user yang sedang login
                        })
                            ->where('overtime_date', $value)  // Mencari overtime yang sudah ada pada tanggal yang sama
                            ->whereIn('status', ['pending', 'approved'])  // Mengecek jika statusnya pending atau approved
                            ->exists();

                        if ($existingOvertime) {
                            session()->flash('error', 'You already have an overtime request for this date.');
                            $fail('You already have an overtime request for this date.');
                        }
                    }
                ],
                'duration' => 'required|integer|in:1,2',
                'notes' => 'required|string|max:255',
                'manager_id' => 'required|exists:users,user_id',
                'attachment' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Simpan file jika ada
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('overtime_attachments', 'public');
            }


            // Membuat data overtime
            Overtime::create([
                'employee_id' => auth()->user()->employee->employee_id,  // Mendapatkan employee_id dari user yang sedang login
                'overtime_date' => $request->overtime_date,
                'duration' => $request->duration,
                'notes' => $request->notes,
                'attachment' => $attachmentPath,
                'manager_id' => $request->manager_id,  // Mendapatkan manager_id dari form'
                'status' => 'pending',  // Status default untuk overtime yang diajukan
            ]);

            Log::info('Overtime created successfully', [
                'employee_id' => $request->employee_id,
                'overtime_id' => $request->id,
                'attachment'  => $attachmentPath,
            ]);

            // Redirect ke halaman overtime index dengan pesan sukses
            return redirect()->route('overtime.index')->with('success', 'Overtime request has been successfully added!');
        } catch (\Exception $e) {
            Log::error('Failed to create overtime', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('error', 'Failed to submit overtime request. Please try again.');
        }
    }


    public function approvals()
    {
        $managerId = auth()->id(); // ID manager yang login

        // Pengajuan overtime yang pending untuk manager ini
        $pendingOvertimes = Overtime::where('status', 'pending')
            ->where('manager_id', $managerId)
            ->get();

        // Riwayat overtime (approved/rejected) untuk manager ini
        $historyOvertimes = Overtime::whereIn('status', ['approved', 'rejected'])
            ->where('manager_id', $managerId)
            ->get();

        return view('Superadmin.overtime.approve', compact('pendingOvertimes', 'historyOvertimes'));
    }

    public function approve($id)
    {
        $overtime = Overtime::where('id', $id)
            ->where('manager_id', auth()->id()) // Hanya manager terkait
            ->firstOrFail();

        $overtime->update(['status' => 'approved']);

        return redirect()->route('overtime.approvals')
            ->with('success', 'Overtime request successfully approved!');
    }

    public function reject($id)
    {
        $overtime = Overtime::where('id', $id)
            ->where('manager_id', auth()->id()) // Hanya manager terkait
            ->firstOrFail();

        $overtime->update(['status' => 'rejected']);

        return redirect()->route('overtime.approvals')
            ->with('success', 'Overtime request successfully rejected!');
    }
}
