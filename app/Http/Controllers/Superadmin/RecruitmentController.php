<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\UserCreated; // Import Event untuk mengirim email
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeUserMail; // Import Mailable untuk pengiriman email

class RecruitmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:recruitment.index')->only(['index', 'show']);
        $this->middleware('permission:recruitment.create')->only(['create', 'store']);
        $this->middleware('permission:recruitment.edit')->only(['edit', 'update']);
        $this->middleware('permission:recruitment.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        // Get the search value from the query string
        $search = $request->query('search');

        // Apply search filter if search term exists
        $recruitments = Recruitment::when($search, function ($query, $search) {
            return $query
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('apply_position', 'like', "%{$search}%");
        })->paginate(10);

        return view('Superadmin.recruitment.index', compact('recruitments'));
    }

    public function create()
    {
        return view('Superadmin.recruitment.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate(
            [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:recruitments,email',
                'phone_number' => 'required|numeric',
                'date_of_birth' => 'nullable|date',
                'last_education' => 'required|string|in:Elementary School,Junior High School,Senior High School,Vocational High School,Associate Degree 1,Associate Degree 2,Associate Degree 3,Bachelor’s Degree,Master’s Degree,Doctoral Degree',
                'last_position' => 'required|string',
                'apply_position' => 'required|string',
                'cv_file' => 'required|file|mimes:pdf,doc,docx',
                'remarks' => 'required|string',
                'status' => 'nullable|in:Initial Interview,User Interview 1,User Interview 2,Background Check,Offering letter,Accept,Decline',
            ],
            [
                'phone_number.numeric' => 'Phone number must contain only numbers.',
            ],
        );

        try {
            // Create recruitment record
            $recruitment = Recruitment::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'date_of_birth' => $validated['date_of_birth'],
                'last_education' => $validated['last_education'],
                'last_position' => $validated['last_position'],
                'apply_position' => $validated['apply_position'],
                'cv_file' => $request->file('cv_file')->store('cv_files', 'public'),
                'remarks' => $validated['remarks'],
                'status' => $validated['status'],
            ]);

            if ($recruitment && $recruitment->status === 'Accept') {
                $existingEmployee = Employee::where('recruitment_id', $recruitment->recruitment_id)->first();

                if (!$existingEmployee) {
                    // Buat employee dulu
                    $employee = Employee::create([
                        'recruitment_id' => $recruitment->recruitment_id,
                        'first_name' => $recruitment->first_name,
                        'last_name' => $recruitment->last_name,
                        'email' => $recruitment->email,
                        'phone_number' => $recruitment->phone_number,
                        'date_of_birth' => $recruitment->date_of_birth,
                        'last_education' => $recruitment->last_education,
                        'cv_file' => $recruitment->cv_file,
                        'status' => 'Active',
                    ]);

                    // Buat user dengan employee_id
                    $user = User::create([
                        'recruitment_id' => $recruitment->recruitment_id,
                        'employee_id' => $employee->employee_id, // Baru disini tidak undefined
                        'name' => $recruitment->first_name . ' ' . $recruitment->last_name,
                        'email' => $recruitment->email,
                        'password' => bcrypt('defaultPassword'),
                    ]);

                    // Update user_id di tabel employee
                    $employee->update([
                        'user_id' => $user->user_id,
                    ]);

                    // Update recruitment
                    $recruitment->update([
                        'employee_id' => $employee->employee_id,
                        'user_id' => $user->user_id,
                    ]);

                    // Trigger event to send email to the newly created user
                    event(new UserCreated($user, 'defaultPassword'));
                }
            }

            return redirect()->route('recruitment.index')->with('success', 'Recruitment successfully created');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function edit($recruitment_id)
    {
        $recruitment = Recruitment::findOrFail($recruitment_id);
        return view('Superadmin.recruitment.edit', compact('recruitment'));
    }

    public function update(Request $request, $recruitment_id)
    {
        $recruitment = Recruitment::findOrFail($recruitment_id);

        $validated = $request->validate(
            [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:recruitments,email,' . $recruitment->recruitment_id . ',recruitment_id',
                'phone_number' => 'nullable|numeric',
                'date_of_birth' => 'nullable|date',
                'last_education' => 'nullable|string|in:Elementary School,Junior High School,Senior High School,Vocational High School,Associate Degree 1,Associate Degree 2,Associate Degree 3,Bachelor’s Degree,Master’s Degree,Doctoral Degree',
                'last_position' => 'nullable|string',
                'apply_position' => 'nullable|string',
                'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'remarks' => 'nullable|string',
                'status' => 'nullable|in:Initial Interview,User Interview 1,User Interview 2,Background Check,Offering letter,Accept,Decline',
            ],
            [
                'phone_number.numeric' => 'Phone number must contain only numbers.',
            ],
        );

        try {
            // Update Recruitment data
            $recruitment->first_name = $validated['first_name'] ?? $recruitment->first_name;
            $recruitment->last_name = $validated['last_name'] ?? $recruitment->last_name;
            $recruitment->email = $validated['email'] ?? $recruitment->email;
            $recruitment->phone_number = $validated['phone_number'] ?? $recruitment->phone_number;
            $recruitment->date_of_birth = $validated['date_of_birth'] ?? $recruitment->date_of_birth;
            $recruitment->last_education = $validated['last_education'] ?? $recruitment->last_education;
            $recruitment->last_position = $validated['last_position'] ?? $recruitment->last_position;
            $recruitment->apply_position = $validated['apply_position'] ?? $recruitment->apply_position;
            $recruitment->status = $validated['status'] ?? $recruitment->status;

            if ($request->hasFile('cv_file')) {
                $recruitment->cv_file = $request->file('cv_file')->store('cv_files', 'public');
            }

            $recruitment->remarks = $validated['remarks'] ?? $recruitment->remarks;
            $recruitment->save();

            if (strtolower($recruitment->status) === 'accept') {
                $existingEmployee = Employee::where('recruitment_id', $recruitment->recruitment_id)->first();

                if (!$existingEmployee) {
                    // Buat Employee
                    $employee = Employee::create([
                        'recruitment_id' => $recruitment->recruitment_id,
                        'first_name' => $recruitment->first_name,
                        'last_name' => $recruitment->last_name,
                        'email' => $recruitment->email,
                        'phone_number' => $recruitment->phone_number,
                        'date_of_birth' => $recruitment->date_of_birth,
                        'last_education' => $recruitment->last_education,
                        'cv_file' => $recruitment->cv_file,
                        'status' => 'Active',
                    ]);

                    // Buat User dengan employee_id
                    $user = User::create([
                        'recruitment_id' => $recruitment->recruitment_id,
                        'employee_id' => $employee->employee_id, 
                        'name' => $recruitment->first_name . ' ' . $recruitment->last_name,
                        'email' => $recruitment->email,
                        'password' => bcrypt('defaultPassword'),
                    ]);

                    // Update user_id di Employee
                    $employee->update([
                        'user_id' => $user->user_id,
                    ]);

                    // Update Recruitment
                    $recruitment->update([
                        'employee_id' => $employee->employee_id,
                        'user_id' => $user->user_id,
                    ]);

                    // Trigger Email
                    event(new UserCreated($user, 'defaultPassword'));
                }
            }

            return redirect()->route('recruitment.index')->with('success', 'Recruitment updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function show($recruitment_id)
    {
        $recruitment = Recruitment::findOrFail($recruitment_id);
        return view('Superadmin.recruitment.show', compact('recruitment'));
    }

    public function destroy($recruitment_id)
    {
        $recruitment = Recruitment::findOrFail($recruitment_id);
        $recruitment->delete();

        return redirect()->route('recruitment.index')->with('success', 'Recruitment deleted successfully');
    }
}
