@extends('layouts.app')
@section('title', 'Payroll/edit')
@section('content')
<div class="container">
    <h2>Edit Payroll</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('payroll.update', $payroll->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="employee_id" class="form-label">Nama Karyawan</label>
            <select name="employee_id" class="form-select" required>
                <option value="">Pilih Karyawan</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $payroll->employee_id == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="allowance" class="form-label">Tunjangan</label>
            <input type="number" name="allowance" class="form-control" value="{{ old('allowance', $payroll->allowance) }}">
        </div>

        <div class="mb-3">
            <label for="overtime" class="form-label">Lembur</label>
            <input type="number" name="overtime" class="form-control" value="{{ old('overtime', $payroll->overtime) }}">
        </div>

        <div class="mb-3">
            <label for="deductions" class="form-label">Potongan</label>
            <input type="number" name="deductions" class="form-control" value="{{ old('deductions', $payroll->deductions) }}">
        </div>

        <button type="submit" class="btn btn-primary">Update Payroll</button>
    </form>
</div>
@endsection
