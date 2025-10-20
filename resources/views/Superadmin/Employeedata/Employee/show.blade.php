@extends('layouts.app')
@section('title', 'Employee/show')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.index') }}" class="btn btn-secondary" title="Back to List">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>First Name</th>
                            <td>{{ $employee->first_name }}</td>
                        </tr>
                        <tr>
                            <th>Last Name</th>
                            <td>{{ $employee->last_name }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>{{ $employee->employee_number }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                        <tr>
                            <th>Place of Birth</th>
                            <td>{{ $employee->place_birth }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td>{{ \Carbon\Carbon::parse($employee->date_birth)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Identity Number</th>
                            <td>{{ $employee->identity_number }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $employee->address }}</td>
                        </tr>
                        <tr>
                            <th>Current Address</th>
                            <td>{{ $employee->current_address }}</td>
                        </tr>
                        <tr>
                            <th>Check IN Time</th>
                            <td>{{ $employee->division->check_in_time }}</td>
                        </tr>
                        <tr>
                            <th>Check OUT Time</th>
                            <td>{{ $employee->division->check_out_time }}</td>
                        </tr>
                        <tr>
                            <th>Division</th>
                            <td>{{ $employee->division->name }}</td>
                        </tr>
                        <tr>
                            <th>Employee Type</th>
                            <td>{{ $employee->employee_type }}</td>
                        </tr>
                        <tr>
                            <th>Blood Type</th>
                            <td>{{ $employee->blood_type }}</td>
                        </tr>
                        <tr>
                            <th>Blood Rhesus</th>
                            <td>{{ $employee->blood_rhesus }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number</th>
                            <td>{{ $employee->phone_number }}</td>
                        </tr>
                        <tr>
                            <th>HP Number</th>
                            <td>{{ $employee->hp_number }}</td>
                        </tr>
                        <tr>
                            <th>Marital Status</th>
                            <td>{{ $employee->marital_status }}</td>
                        </tr>
                        <tr>
                            <th>CV File</th>
                            <td>
                                <a href="{{ Storage::url($employee->cv_file) }}" target="_blank">
                                    View CV
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Last CV Update</th>
                            <td>
                                @if ($employee->update_cv)
                                    {{ \Carbon\Carbon::parse($employee->update_cv)->format('d F Y ') }}
                                @else
                                    There is no update information from CV
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Last Education</th>
                            <td>{{ $employee->last_education }}</td>
                        </tr>
                        <tr>
                            <th>Degree</th>
                            <td>{{ $employee->degree }}</td>
                        </tr>
                        <tr>
                            <th>Starting Date</th>
                            <td>{{ $employee->starting_date }}</td>
                        </tr>
                        <tr>
                            <th>Interview By</th>
                            <td>{{ $employee->interview_by }}</td>
                        </tr>
                        <tr>
                            <th>Current Salary</th>
                            <td>Rp {{ number_format($employee->current_salary, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Attendance Allowance</th>
                            <td>Rp {{ number_format($employee->attendance_allowance, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Positional Allowance</th>
                            <td>Rp {{ number_format($employee->positional_allowance, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Transport Allowance</th>
                            <td>Rp {{ number_format($employee->transport_allowance, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Bonus</th>
                            <td>Rp {{ number_format($employee->bonus_allowance, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Insurance</th>
                            <td>{{ $employee->insurance ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th>Serious Illness</th>
                            <td>{{ $employee->serious_illness }}</td>
                        </tr>
                        <tr>
                            <th>Hereditary Disease</th>
                            <td>{{ $employee->hereditary_disease }}</td>
                        </tr>
                        <tr>
                            <th>Emergency Contact</th>
                            <td>{{ $employee->emergency_contact }}</td>
                        </tr>
                        <tr>
                            <th>Relations</th>
                            <td>{{ $employee->relations }}</td>
                        </tr>
                        <tr>
                            <th>Emergency Number</th>
                            <td>{{ $employee->emergency_number }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $employee->status == 'Active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
