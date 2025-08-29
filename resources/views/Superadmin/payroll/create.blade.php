@extends('layouts.app')
@section('title', 'Payroll/create')
@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Payroll</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('payroll.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="allowance">Allowance</label>
                        <input type="number" name="allowance" id="allowance" class="form-control" value="{{ old('allowance', 0) }}" step="0.01">
                        @error('allowance')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="overtime">Overtime</label>
                        <input type="number" name="overtime" id="overtime" class="form-control" value="{{ old('overtime', 0) }}" step="0.01">
                        @error('overtime')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deductions">Deductions</label>
                        <input type="number" name="deductions" id="deductions" class="form-control" value="{{ old('deductions', 0) }}" step="0.01">
                        @error('deductions')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Save Payroll</button>
                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </section>
@endsection
