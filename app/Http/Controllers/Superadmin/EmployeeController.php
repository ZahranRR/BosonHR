<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\UserCreated; // Import Event untuk mengirim email
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeUserMail; // Import Mailable untuk pengiriman email
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employee.index')->only('index', 'show', 'searchEmployees');
        $this->middleware('permission:employee.create')->only(['create', 'store']);
        $this->middleware('permission:employee.edit')->only(['edit', 'update']);
        $this->middleware('permission:employee.delete')->only('destroy');
    }
    public function index(Request $request)
    {
        $search = $request->get('search');

        $employees = Employee::query()
            ->where(function ($query) use ($search) {
                $query
                    ->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            })
            ->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'employees' => $employees,
            ]);
        }
        // Jika pencarian tidak menemukan hasil
        if ($employees->isEmpty() && $search) {
            return redirect()
                ->route('employee.index')
                ->withErrors(['No data found for the search term: ' . $search]);
        }

        return view('Superadmin.Employeedata.Employee.index', compact('employees'));
    }

    public function create(): View
    {
        // Ambil semua data divisions
        $divisions = Division::all();

        return view('Superadmin.Employeedata.Employee.create', compact('divisions'));
    }

    // public function store(Request $request)
    // {
    //     // Validasi input
    //     $validatedData = $request->validate(
    //         [
    //             'first_name' => 'required|string',
    //             'last_name' => 'required|string',
    //             'email' => 'required|email|unique:employees,email',
    //             'check_in_time' => 'required|date_format:H:i',
    //             'check_out_time' => 'required|date_format:H:i|after:check_in_time',
    //             'division_id' => 'required|exists:divisions,id',
    //             'place_birth' => 'required|string',
    //             'date_birth' => 'nullable|date',
    //             'identity_number' => 'nullable|regex:/^[0-9]+$/|max:20',
    //             'address' => 'nullable|string',
    //             'current_address' => 'nullable|string',
    //             'blood_type' => 'nullable|in:A,B,AB,O',
    //             'blood_rhesus' => 'nullable|string',
    //             'phone_number' => 'required|numeric',
    //             'hp_number' => 'required|numeric',
    //             'marital_status' => 'nullable|in:Single,Married,Widow,Widower',
    //             'cv_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
    //             // 'update_cv' => 'nullable|string',
    //             'last_education' => 'nullable|in:Elementary School,Junior High School,Senior High School,Vocational High School,Associate Degree 1,Associate Degree 2,Associate Degree 3,Bachelor’s Degree,Master’s Degree,Doctoral Degree',
    //             'degree' => 'nullable|string',
    //             'starting_date' => 'nullable|date',
    //             'interview_by' => 'nullable|string',
    //             'current_salary' => 'nullable|string',
    //             'insurance' => 'nullable|boolean',
    //             'serious_illness' => 'nullable|string',
    //             'hereditary_disease' => 'nullable|string',
    //             'emergency_contact' => 'nullable|string',
    //             'relations' => 'nullable|in:Parent,Guardian,Husband,Wife,Sibling',
    //             'emergency_number' => 'required|numeric',
    //             'status' => 'nullable|in:Active,Inactive,Pending,Suspend',
    //         ],
    //         [
    //             'identity_number.regex' => 'Identity number must only contain numbers.',
    //             'identity_number.max' => 'Identity number cannot exceed 20 digits.',
    //             'phone_number.numeric' => 'Phone number must contain only numbers.',
    //             'phone_number.max' => 'Phone number cannot exceed 15 digits.',
    //             'hp_number.numeric' => 'HP number must only contain numbers.',
    //             'hp_number.max' => 'HP number cannot exceed 15 digits.',
    //             'emergency_number.numeric' => 'Emergency number must only contain numbers.',
    //             'emergency_number.max' => 'Emergency number cannot exceed 15 digits.',
    //         ],
    //     );

    //     // Hapus pemisah ribuan pada `current_salary` dan konversi ke integer
    //     if (isset($request->current_salary)) {
    //         $validatedData['current_salary'] = (int) str_replace('.', '', $request->current_salary);
    //     }

    //     // Proses unggah file CV
    //     if ($request->hasFile('cv_file')) {
    //         $file = $request->file('cv_file');
    //         $fileName = time() . '_' . $file->getClientOriginalName();
    //         $filePath = $file->storeAs('cv_files', $fileName, 'public');
    //         $validatedData['cv_file'] = $filePath;
    //     }

    //     // Generate NIK
    //     $division = Division::find($request->division_id);

    //     $lastEmployee = Employee::where('division_id', $division->id)
    //         ->orderBy('employee_number', 'desc')
    //         ->first();

    //     $nextNumber = $lastEmployee ? intval(substr($lastEmployee->employee_number, -3)) + 1 : 1;

    //     $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    //     $yearBirth = date('y', strtotime($request->date_birth));
    //     $yearStart = date('y', strtotime($request->starting_date));
    //     $validatedData['employee_number'] = sprintf('%03d/%02d%02d/%s', $division->id, $yearBirth, $yearStart, $formattedNumber);

    //     try {
    //         // Menyimpan data Employee
    //         $employee = Employee::create($validatedData);

    //         // Membuat data User
    //         $user = User::create([
    //             'name' => $request->first_name . ' ' . $request->last_name,
    //             'email' => $request->email,
    //             'password' => bcrypt('defaultpassword'), // Default password
    //             'employee_id' => $employee->employee_id,
    //         ]);

    //         // Menyimpan ID User ke Employee
    //         $employee->user_id = $user->user_id;
    //         $employee->save();

    //         //Notif error create user
    //         if (!$user) {
    //             return redirect()->route('employee.index')->withErrors('Failed to create user account!');
    //         }

    //         // Kirim email kepada user yang baru dibuat
    //         // Mail::to($user->email)->send(new WelcomeUserMail($user, 'defaultpassword'));

    //         return redirect()
    //             ->route('datauser.edit', $user->user_id)
    //             ->with('success', 'Employee created successfully.');
    //     } catch (\Exception $e) {
    //         return back()->withErrors($e->getMessage());
    //     }
    // }


    public function store(Request $request)
    {
        Log::info('EmployeeController@store called', [
            'request' => $request->all()
        ]);

        try {
            // VALIDASI
            $validatedData = $request->validate(
                [
                    'first_name' => 'required|string',
                    'last_name' => 'required|string',
                    'email' => 'required|email|unique:employees,email',
                    'check_in_time' => 'required|date_format:H:i',
                    'check_out_time' => 'required|date_format:H:i|after:check_in_time',
                    'division_id' => 'required|exists:divisions,id',
                    'place_birth' => 'required|string',
                    'date_birth' => 'nullable|date',
                    'identity_number' => 'nullable|regex:/^[0-9]+$/|max:20',
                    'address' => 'nullable|string',
                    'current_address' => 'nullable|string',
                    'blood_type' => 'nullable|in:A,B,AB,O',
                    'blood_rhesus' => 'nullable|string',
                    'phone_number' => 'required|numeric',
                    'hp_number' => 'required|numeric',
                    'marital_status' => 'nullable|in:Single,Married,Widow,Widower',
                    'cv_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
                    'last_education' => 'nullable|string',
                    'degree' => 'nullable|string',
                    'starting_date' => 'nullable|date',
                    'interview_by' => 'nullable|string',
                    'current_salary' => 'nullable|string',
                    'insurance' => 'nullable|boolean',
                    'serious_illness' => 'nullable|string',
                    'hereditary_disease' => 'nullable|string',
                    'emergency_contact' => 'nullable|string',
                    'relations' => 'nullable|in:Parent,Guardian,Husband,Wife,Sibling',
                    'emergency_number' => 'required|numeric',
                    'status' => 'nullable|in:Active,Inactive,Pending,Suspend',
                ]
            );
            Log::info('Validation passed', $validatedData);

            // Salary
            if (isset($request->current_salary)) {
                $validatedData['current_salary'] = (int) str_replace('.', '', $request->current_salary);
                Log::info('current_salary cleaned', ['current_salary' => $validatedData['current_salary']]);
            }

            // FILE
            if ($request->hasFile('cv_file')) {
                $file = $request->file('cv_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('cv_files', $fileName, 'public');
                $validatedData['cv_file'] = $filePath;
                Log::info('CV file uploaded', ['path' => $filePath]);
            } else {
                Log::warning('cv_file missing');
            }

            // NIK
            $division = Division::find($request->division_id);
            if (!$division) {
                Log::error('Division not found', ['division_id' => $request->division_id]);
                return back()->withErrors(['division_id' => 'Division not found']);
            }

            $lastEmployee = Employee::where('division_id', $division->id)
                ->orderBy('employee_number', 'desc')
                ->first();
            Log::info('Last employee', ['lastEmployee' => $lastEmployee]);

            $nextNumber = $lastEmployee ? intval(substr($lastEmployee->employee_number, -3)) + 1 : 1;
            $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $yearBirth = $request->date_birth ? date('y', strtotime($request->date_birth)) : '00';
            $yearStart = $request->starting_date ? date('y', strtotime($request->starting_date)) : '00';

            $validatedData['employee_number'] = sprintf(
                '%03d/%s%s/%s',
                $division->id,
                $yearBirth,
                $yearStart,
                $formattedNumber
            );

            Log::info('Generated employee_number', ['employee_number' => $validatedData['employee_number']]);

            // EMPLOYEE SAVE
            $employee = Employee::create($validatedData);
            Log::info('Employee saved', ['employee' => $employee]);

            // USER SAVE
            $user = User::create([
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $employee->email,
                'password' => bcrypt('defaultpassword'),
                'employee_id' => $employee->employee_id
            ]);
            Log::info('User saved', ['user' => $user]);

            // LINK EMPLOYEE-USER
            $employee->user_id = $user->user_id;
            $employee->save();
            Log::info('Employee updated with user_id', [
                'employee_id' => $employee->employee_id,
                'user_id' => $employee->user_id
            ]);

            if (!$user) {
                Log::error('Failed to create user');
                return redirect()->route('employee.index')->withErrors('Failed to create user account!');
            }

            // MAIL
            try {
                Mail::to($user->email)->send(new WelcomeUserMail($user, 'defaultpassword'));
                Log::info('Welcome email sent', ['email' => $user->email]);
            } catch (\Throwable $mailError) {
                Log::error('Mail send failed', [
                    'message' => $mailError->getMessage(),
                    'file' => $mailError->getFile(),
                    'line' => $mailError->getLine(),
                ]);
            }

            Log::info('store() completed successfully');
            return redirect()->route('datauser.edit', ['user_id' => $user->user_id])->with('success', 'Employee created successfully.');
        } catch (\Throwable $th) {
            Log::error('Store failed', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            return back()->withErrors('Failed to create employee: ' . $th->getMessage());
        }
    }



    public function edit($employee)
    {
        $employeeModel = Employee::where('employee_id', $employee)->first();

        if (!$employeeModel) {
            return redirect()->route('employee.index')->with('error', 'Data tidak ditemukan!');
        }

        // Ambil semua data divisi
        $divisions = Division::all();

        // Kirimkan $divisions ke view
        return view('Superadmin.Employeedata.Employee.update', compact('employeeModel', 'divisions'));
    }

    public function update(Request $request, $employeeId)
    {
        $employeeModel = Employee::findOrFail($employeeId);

        // Menambahkan detik "00" ke waktu jika belum ada
        if ($request->has('check_in_time') && strpos($request->check_in_time, ':') === strrpos($request->check_in_time, ':')) {
            $request->merge([
                'check_in_time' => $request->input('check_in_time') . ':00',
                'check_out_time' => $request->input('check_out_time') . ':00',
            ]);
        }

        // Validasi input
        $validatedData = $request->validate(
            [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:employees,email,' . $employeeModel->employee_id . ',employee_id',
                'check_in_time' => 'nullable|date_format:H:i:s',
                'check_out_time' => 'nullable|date_format:H:i:s|after:check_in_time',
                'division_id' => 'nullable|exists:divisions,id',
                'place_birth' => 'required|string',
                'date_birth' => 'nullable|date',
                'identity_number' => 'nullable|regex:/^[0-9]+$/|max:20',
                'address' => 'nullable|string',
                'current_address' => 'nullable|string',
                'blood_type' => 'nullable|in:A,B,AB,O',
                'blood_rhesus' => 'nullable|string',
                'phone_number' => 'required|numeric',
                'hp_number' => 'required|numeric',
                'marital_status' => 'nullable|in:Single,Married,Widow,Widower',
                'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'update_cv' => 'nullable|string',
                'last_education' => 'nullable|in:Elementary School,Junior High School,Senior High School,Vocational High School,Associate Degree 1,Associate Degree 2,Associate Degree 3,Bachelor’s Degree,Master’s Degree,Doctoral Degree',
                'degree' => 'nullable|string',
                'starting_date' => 'nullable|date',
                'interview_by' => 'nullable|string',
                'current_salary' => 'nullable|string',
                'insurance' => 'nullable|boolean',
                'serious_illness' => 'nullable|string',
                'hereditary_disease' => 'nullable|string',
                'emergency_contact' => 'nullable|string',
                'relations' => 'nullable|in:Parent,Guardian,Husband,Wife,Sibling',
                'emergency_number' => 'required|numeric',
                'status' => 'nullable|in:Active,Inactive,Pending,Suspend',
            ],
            [
                'identity_number.regex' => 'Identity number must only contain numbers.',
                'identity_number.max' => 'Identity number cannot exceed 20 digits.',
                'phone_number.numeric' => 'Phone number must contain only numbers.',
                'phone_number.max' => 'Phone number cannot exceed 15 digits.',
                'hp_number.numeric' => 'HP number must only contain numbers.',
                'hp_number.max' => 'HP number cannot exceed 15 digits.',
                'emergency_number.numeric' => 'Emergency number must only contain numbers.',
                'emergency_number.max' => 'Emergency number cannot exceed 15 digits.',
            ],
        );

        // Menghapus pemisah ribuan dan mengonversi ke integer
        if (isset($request->current_salary)) {
            $validatedData['current_salary'] = (int) str_replace('.', '', $request->input('current_salary'));
        }

        // Proses penggantian file CV hanya jika ada file CV yang diunggah
        if ($request->hasFile('cv_file')) {
            // Hapus file CV lama jika ada
            if ($employeeModel->cv_file) {
                Storage::disk('public')->delete($employeeModel->cv_file);
            }

            // Simpan file CV baru
            $file = $request->file('cv_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('cv_files', $fileName, 'public');
            $validatedData['cv_file'] = $filePath;

            // Update waktu terakhir CV diunggah
            $validatedData['update_cv'] = now();
        }

        // Cek perubahan terkait NIK
        $isNikChanged = false;
        $divisionChanged = $request->input('division_id') != $employeeModel->division_id;

        if (
            $divisionChanged ||
            $request->input('date_birth') != $employeeModel->date_birth ||
            $request->input('starting_date') != $employeeModel->starting_date
        ) {
            $isNikChanged = true;
        }

        // Update data karyawan
        $employeeModel->fill($validatedData);

        try {
            $employeeModel->save();

            // Perbarui NIK jika ada perubahan
            if ($isNikChanged) {
                $divisionCode = str_pad($request->input('division_id'), 3, '0', STR_PAD_LEFT); // Kode Divisi
                $yearBirth = $request->input('date_birth') ? substr($request->input('date_birth'), 2, 2) : '00'; // Tahun Lahir
                $yearStart = $request->input('starting_date') ? substr($request->input('starting_date'), 2, 2) : '00'; // Tahun Masuk

                if ($divisionChanged) {
                    // Jika divisi berubah, dapatkan nomor urut terakhir di divisi baru
                    $sequence = Employee::where('division_id', $request->input('division_id'))->count() + 1;
                } else {
                    // Jika hanya tahun lahir atau tahun masuk berubah, nomor urut tetap
                    $sequence = (int) explode('/', $employeeModel->employee_number)[2];
                }

                // Format NIK baru
                $newEmployeeNumber = sprintf('%03d/%s%s/%03d', $divisionCode, $yearBirth, $yearStart, $sequence);
                $employeeModel->employee_number = $newEmployeeNumber;
                $employeeModel->save();
            }

            // Update data user yang terkait jika ada
            if ($employeeModel->user) {
                $employeeModel->user->name = $request->input('first_name') . ' ' . $request->input('last_name');
                $employeeModel->user->email = $request->input('email');
                $employeeModel->user->save();
            }

            return redirect()->route('employee.index')->with('success', 'Data Employee berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->first();

        $employee->delete();
        return redirect()->route('employee.index')->with('success', 'Data berhasil dihapus!');
    }

    public function show($employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();

        return view('Superadmin.Employeedata.Employee.show', compact('employee'));
    }

    public function searchEmployees(Request $request)
    {
        $query = $request->get('query');
        $employees = Employee::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orderBy('first_name')
            ->get(['employee_id', 'first_name', 'last_name']);

        $employees = $employees->map(function ($employee) {
            $employee->full_name = $employee->first_name . ' ' . $employee->last_name;
            return $employee;
        });

        return response()->json($employees);
    }
}
