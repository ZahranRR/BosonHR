    @extends('layouts.app')
@section('title', 'Employee/index')
@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h3 class="card-title mb-0">Employee</h3>

                    <div class="d-flex align-items-center">
                        <a href="{{ route('employee.create') }}" class="btn btn-primary" title="Create Employee">
                            <i class="fas fa-plus"></i> Add
                        </a>

                        <form action="{{ route('employee.index') }}" method="GET" class="form-inline ml-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email..."
                                value="{{ request()->query('search') }}" >
                            <button type="submit" class="btn btn-secondary ml-2">Search</button>
                        </form>
                    </div>
                </div>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>

            @if ($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let errorMessages = '';
                        @foreach ($errors->all() as $error)
                            errorMessages += '{{ $error }}\n';
                        @endforeach

                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: errorMessages,
                        });
                    });
                </script>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th class="text-left" style="width: 20%;">First Name</th>
                                <th class="text-center" style="width: 20%;">Last Name</th>
                                <th class="text-center" style="width: 20%;">Email</th>
                                <th class="text-center" style="width: 20%;">Address</th>
                                <th class="text-right" style="width: 20%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr data-url="{{ route('employee.show', $employee->employee_id) }}" class="clickable-row">
                                    <td class="text-left">{{ $employee->first_name }}</td>
                                    <td class="text-center">{{ $employee->last_name }}</td>
                                    <td class="text-center">{{ $employee->email }}</td>
                                    <td class="text-center">{{ $employee->address }}</td>
                                    <td class="project-actions text-right">
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('employee.edit', $employee->employee_id) }}">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                        <form method="POST"
                                            action="{{ route('employee.destroy', $employee->employee_id) }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="deletebutton btn btn-danger btn-sm">
                                                <i class="deletebutton fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="pagination-container">
                        {{ $employees->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
