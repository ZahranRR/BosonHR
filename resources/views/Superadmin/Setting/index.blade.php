@extends('layouts.app')
@section('title', 'Settings/index')
@section('content')
    <section class="content">
        @can('settings.company')
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Company Settings</h3>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($companyname) ? route('settings.update', $companyname->id) : route('settings.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($companyname))
                            @method('PUT')
                        @endif

                        <div class="form-group mb-4">
                            <label for="name_company" class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="name_company" id="name_company"
                                value="{{ isset($companyname) ? $companyname->name_company : '' }}" required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="image" class="form-label">Company Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="image" id="image" accept="image/*">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            <div class="mt-2">
                                @if (isset($companyname) && $companyname->image)
                                    <p>Current Image:</p>
                                    <img src="{{ asset('storage/' . $companyname->image) }}" alt="Current Image"
                                        class="img-fluid img-thumbnail" width="150">
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ isset($companyname) ? 'Update' : 'Create' }}</button>
                    </form>
                </div>
            </div>
        @endcan

        @can('settings.deductions')
            <div class="card mt-4">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Salary Deduction Settings</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.salarydeductions') }}" method="POST">
                        @csrf

                        <div class="form-group mb-4">
                            <label for="late_deduction" class="form-label">Late Check-in Deduction</label>
                            <input type="text" class="form-control" name="late_deduction" id="late_deduction"
                                value="{{ isset($salaryDeduction) ? number_format($salaryDeduction->late_deduction, 0, ',', '.') : '' }}"
                                required onkeyup="formatNumber(this)">
                        </div>

                        <div class="form-group mb-4">
                            <label for="early_deduction" class="form-label">Early Check-out Deduction</label>
                            <input type="text" class="form-control" name="early_deduction" id="early_deduction"
                                value="{{ isset($salaryDeduction) ? number_format($salaryDeduction->early_deduction, 0, ',', '.') : '' }}"
                                required onkeyup="formatNumber(this)">
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        @endcan

        @can('settings.worksdays')
            <div class="card mt-4">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Workday Settings</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.updateWorkdays') }}" method="POST">
                        @csrf
                        <label for="effective_days">Select Effective Workdays</label><br>

                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="effective_days[]"
                                    value="{{ $day }}"
                                    {{ in_array($day, $workdaySetting->effective_days ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $day }}</label>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary mt-3">Save</button>
                    </form>
                </div>
            </div>
        @endcan
    </section>

    <script>
        document.querySelectorAll('form button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: 'Your work has been saved',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    this.closest('form').submit();
                });
            });
        });
    </script>
@endsection

