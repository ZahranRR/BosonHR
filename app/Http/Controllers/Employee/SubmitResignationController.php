<?php

namespace App\Http\Controllers\Employee;

use App\Models\ResignationRequest;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubmitResignationController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('permission:submitresign.index')->only(['index', 'searchEmployees']);
        $this->middleware('permission:submitresign.create')->only(['create', 'store']);
    }

    public function index()
    {
        $resignationRequests = ResignationRequest::with('employee')
            ->where('status', 'approved') 
            ->whereNull('manager_id')
            ->paginate(10); 
        return view('Employee.submitresign.index', compact('resignationRequests'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('Employee.submitresign.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate(
            [
                'employee_id' => 'required|exists:employees,employee_id',
                'resign_date' => 'required|date|after_or_equal:' . now()->toDateString(),
                'reason' => 'required|string',
                'remarks' => 'nullable|string',
                'name' => 'nullable|string', // Validasi field name jika digunakan
            ],
            [
                'employee_id.required' => 'Employee ID is required.',
                'employee_id.exists' => 'Selected Employee does not exist.',
                'resign_date.required' => 'Resignation date is required.',
                'resign_date.after_or_equal' => 'Resignation date cannot be in the past.',
                'reason.required' => 'Reason for resignation is required.',
            ],
        );

        try {
            // Ambil data employee berdasarkan employee_id
            $employee = Employee::select('first_name', 'last_name')
                ->where('employee_id', $request->employee_id)
                ->first();

            // Pastikan data karyawan ditemukan
            if (!$employee) {
                return redirect()
                    ->back()
                    ->withErrors(['employee_id' => 'Employee not found']);
            }

            // Simpan data pengunduran diri
            ResignationRequest::create([
                'employee_id' => $request->employee_id,
                'resign_date' => $request->resign_date,
                'reason' => $request->reason,
                'remarks' => $request->remarks,
                'status' => 'approved', // Status otomatis disetujui
                'manager_id' => null, // Tidak ada manager untuk submit resignation
                'name' => $employee->first_name . ' ' . $employee->last_name, // Menggunakan nama dari data Employee
            ]);

            // Redirect ke halaman index setelah sukses
            return redirect()->route('submitresign.index')->with('success', 'Resignation submitted and approved automatically!');
        } catch (\Exception $e) {
            // Log error jika ada kesalahan saat menyimpan
            Log::error('Error saving resignation: ', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => 'There was an error while submitting your resignation. Please try again later.']);
        }
    }

    public function searchEmployees(Request $request)
    {
        $query = $request->get('query'); // Mendapatkan query pencarian

        // Log query yang diterima
        Log::info('Search query received: ' . $query);

        // Cari karyawan berdasarkan first_name atau last_name
        try {
            $employees = Employee::where('first_name', 'LIKE', "%{$query}%")
                ->orWhere('last_name', 'LIKE', "%{$query}%")
                ->orderBy('first_name')
                ->get(['employee_id', 'first_name', 'last_name']); // Ambil kolom yang dibutuhkan

            // Log hasil pencarian
            Log::info('Search results: ', ['employees' => $employees->toArray()]);

            // Gabungkan nama depan dan nama belakang sebagai nama lengkap
            $employees = $employees->map(function ($employee) {
                $employee->full_name = $employee->first_name . ' ' . $employee->last_name;
                return $employee;
            });

            return response()->json($employees); // Kembalikan response JSON
        } catch (\Exception $e) {
            // Log error jika ada kesalahan dalam pencarian
            Log::error('Error in employee search: ', [
                'error' => $e->getMessage(),
                'query' => $query,
            ]);

            return response()->json(['error' => 'Error searching for employees'], 500); // Mengirimkan error jika terjadi masalah
        }
    }
}
