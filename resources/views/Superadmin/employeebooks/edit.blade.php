@extends('layouts.app')
@section('title', 'Employeebooks/edit')
@section('content')
    <div class="container-fluid">
        @php
            $categoryTitle = '';
            if ($employeeBook->category === 'violation') {
                $categoryTitle = 'Edit Violation';
            } elseif ($employeeBook->category === 'warning') {
                $categoryTitle = 'Edit Warning';
            } elseif ($employeeBook->category === 'reprimand') {
                $categoryTitle = 'Edit Reprimand';
            }
        @endphp

        <div class="card card-primary w-100">
            <div class="card-header">
                <h3 class="card-title">{{ $categoryTitle }}</h3>
            </div>

            <form id="quickForm" action="{{ route('employeebooks.update', $employeeBook->employeebook_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select name="employee_id" class="form-control" required>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->employee_id }} "
                                    {{ $employeeBook->employee_id == $employee->employee_id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_of">Type of</label>
                        <select name="type_of" id="type_of" class="form-control">
                            <option value="SOP"
                                {{ old('type_of', $employeeBook->type_of ?? '') == 'SOP' ? 'selected' : '' }}>SOP</option>
                            <option value="Administrative"
                                {{ old('type_of', $employeeBook->type_of ?? '') == 'Administrative' ? 'selected' : '' }}>
                                Administrative</option>
                            <option value="Behavior"
                                {{ old('type_of', $employeeBook->type_of ?? '') == 'Behavior' ? 'selected' : '' }}>Behavior
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="incident_date">Incident Date</label>
                        <input type="date" name="incident_date" class="form-control"
                            value="{{ $employeeBook->incident_date }}" required>
                    </div>

                    <div class="form-group">
                        <label for="incident_detail">Incident Detail</label>
                        <textarea name="incident_detail" class="form-control" required>{{ $employeeBook->incident_detail }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" class="form-control" required>{{ $employeeBook->remarks }}</textarea>
                    </div>

                    <input type="hidden" name="category" value="{{ $employeeBook->category }}">
                </div>
                <div class="card-footer">
                    <button type="submit" id="saveButton" class="btn btn-primary">Save</button>
                    <a href="{{ route('employeebooks.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
@endsection
