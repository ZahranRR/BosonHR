<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\Employee;
use App\Models\EmployeeBook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class EmployeeBooksController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employeebook.index')->only('index');
        $this->middleware('permission:employeebook.create')->only(['create', 'store']);
        $this->middleware('permission:employeebook.edit')->only(['edit', 'update']);
        $this->middleware('permission:employeebook.delete')->only('destroy');
        $this->middleware('permission:employeebook.detail')->only('detail');
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $typeOf = $request->query('type_of'); // Ambil nilai dari dropdown "type_of"

        $violations = EmployeeBook::where('category', 'violation')
            ->when($search, function ($query, $search) {
                return $query->whereHas('employee', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($typeOf, function ($query, $typeOf) {
                return $query->where('type_of', $typeOf);
            })
            ->with('employee')
            ->paginate(6);

        $warnings = EmployeeBook::where('category', 'warning')
            ->when($search, function ($query, $search) {
                return $query->whereHas('employee', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($typeOf, function ($query, $typeOf) {
                return $query->where('type_of', $typeOf);
            })
            ->with('employee')
            ->paginate(6);

        $reprimands = EmployeeBook::where('category', 'reprimand')
            ->when($search, function ($query, $search) {
                return $query->whereHas('employee', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($typeOf, function ($query, $typeOf) {
                return $query->where('type_of', $typeOf);
            })
            ->with('employee')
            ->paginate(6);

        $employees = Employee::all(); // Ambil semua data karyawan (jika dibutuhkan)

        // Kirim data ke view
        return view('Superadmin.employeebooks.index', compact('violations', 'warnings', 'reprimands', 'employees', 'search', 'typeOf'));
    }


    public function create(Request $request)
    {
        $employees = Employee::select('employee_id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $category = $request->query('category', 'violation');
        return view('Superadmin.employeebooks.create', compact('employees', 'category'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,employee_id',
                'incident_date' => 'required|date',
                'incident_detail' => 'required|string',
                'remarks' => 'required|string',
                'category' => 'required|string',
                'type_of' => 'required|in:SOP,Administrative,Behavior',
            ]);

            // Pastikan employee_id tidak kosong
            if (empty($request->employee_id)) {
                return redirect()->back()
                    ->with('error', 'Please select a valid employee.')
                    ->withInput();
            }

            // Simpan data ke database
            EmployeeBook::create($request->all());

            return redirect()->route('employeebooks.index', ['category' => $request->category])
                ->with('success', 'Employee book created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in EmployeeBooksController@store: ' . $e->getMessage(), [
                'request_data' => $request->all(),
            ]);

            return redirect()->back()->with('error', 'An error occurred while saving the data. Please try again.');
        }
    }


    public function edit($id)
    {
        $employeeBook = EmployeeBook::findOrFail($id);
        $employees = Employee::all(); // Ambil data karyawan untuk dropdown

        return view('Superadmin.employeebooks.edit', compact('employeeBook', 'employees'));
    }


    public function update(Request $request, EmployeeBook $employeeBook)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'incident_date' => 'required|date',
            'incident_detail' => 'required|string',
            'remarks' => 'required|string',
            'category' => 'required|string',
            'type_of' => 'required|in:SOP,Administrative,Behavior',
        ]);

        $employeeBook->update($request->all());
        return redirect()->route('employeebooks.index')->with('success', 'Employee book updated successfully.');
    }

    public function destroy(EmployeeBook $employeeBook)
    {
        $employeeBook->delete();
        return redirect()->route('employeebooks.index')->with('success', 'Employee book deleted successfully.');
    }

    public function detail(EmployeeBook $employeeBook)
    {

        $employees = Employee::orderBy('first_name')->get();
        return view('Superadmin.employeebooks.detail', compact('employeeBook', 'employees'));
    }

    public function searchEmployees(Request $request)
    {
        $query = $request->get('query'); // Mendapatkan query pencarian

        // Cari karyawan berdasarkan first_name atau last_name
        $employees = Employee::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orderBy('first_name')
            ->get(['employee_id', 'first_name', 'last_name']); // Ambil kolom yang dibutuhkan

        // Gabungkan nama depan dan nama belakang sebagai nama lengkap
        $employees = $employees->map(function ($employee) {
            $employee->full_name = $employee->first_name . ' ' . $employee->last_name;
            return $employee;
        });

        return response()->json($employees); // Kembalikan response JSON
    }
}
