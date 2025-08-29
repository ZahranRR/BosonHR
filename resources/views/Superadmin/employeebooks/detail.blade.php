@extends('layouts.app')
@section('title', 'Employeebooks/detail')
@section('content')
    <div class="container-fluid">
        @php
            $categoryTitle = '';
            if ($employeeBook->category === 'violation') {
                $categoryTitle = 'Violation';
            } elseif ($employeeBook->category === 'warning') {
                $categoryTitle = 'Warning';
            } elseif ($employeeBook->category === 'reprimand') {
                $categoryTitle = 'Reprimand';
            }
        @endphp

        <div class="card card-primary w-100">
            <div class="card-header">
                <h3 class="card-title">{{ $categoryTitle }} Details</h3>
            </div>

            <div class="card-body">
                <!-- Detail Karyawan, tanpa bisa diedit -->
                <div class="form-group">
                    <label for="employee_id">Employee</label>
                    <input type="text" class="form-control"
                        value="{{ $employeeBook->employee->first_name }} {{ $employeeBook->employee->last_name }}" disabled>
                </div>

                <!-- Detail jenis insiden, tanpa bisa diedit -->
                <div class="form-group">
                    <label for="type_of">Type of</label>
                    <input type="text" class="form-control" value="{{ $employeeBook->type_of }}" disabled>
                </div>

                <!-- Detail tanggal insiden, tanpa bisa diedit -->
                <div class="form-group">
                    <label for="incident_date">Incident Date</label>
                    <input type="text" class="form-control" value="{{ $employeeBook->incident_date }}" disabled>
                </div>

                <!-- Detail insiden, tanpa bisa diedit -->
                <div class="form-group">
                    <label for="incident_detail">Incident Detail</label>
                    <textarea class="form-control" disabled>{{ $employeeBook->incident_detail }}</textarea>
                </div>

                <!-- Detail remark, tanpa bisa diedit -->
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" disabled>{{ $employeeBook->remarks }}</textarea>
                </div>
            </div>

            <div class="card-footer">
                <!-- Tombol untuk kembali -->
                <a href="{{ route('employeebooks.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
@endsection
