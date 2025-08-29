@extends('layouts.app')
@section('title', 'Role/edit')
@section('content')
    <div class="content">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-xl-6">
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Role</h3>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <form action="{{ route('role.update', $role->id) }}" method="POST" id="quickForm">
                                    @csrf
                                    @method('PUT')
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="roleName">Role Name</label>
                                            <input type="text" name="name" class="form-control" id="roleName"
                                                value="{{ old('name', $role->name) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="permissions">Permissions Granted</label>

                                            <!-- Select/Deselect All -->
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="selectAllPermissions">
                                                <label class="custom-control-label" for="selectAllPermissions">Select
                                                    All</label>
                                            </div>

                                            <!-- Fitur Dashboard -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <a href="#" id="selectAllDashboard"
                                                        class="card-title">Dashboard</a>
                                                </div>
                                                <div class="card-body">
                                                    @foreach ($permissions->whereIn('name', ['dashboard.superadmin', 'dashboard.employee']) as $permission)
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                class="custom-control-input dashboard-checkbox"
                                                                id="dashboard_{{ $permission->id }}"
                                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="dashboard_{{ $permission->id }}">
                                                                {{ ucfirst(str_replace('dashboard.', '', $permission->name)) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Fitur Role -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <a href="#" id="selectAllRole" class="card-title">Role</a>
                                                </div>
                                                <div class="card-body">
                                                    @foreach ($permissions->whereIn('name', ['role.index', 'role.create', 'role.edit', 'role.delete']) as $permission)
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                class="custom-control-input role-checkbox"
                                                                id="role_{{ $permission->id }}"
                                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="role_{{ $permission->id }}">
                                                                {{ ucfirst(str_replace('role.', '', $permission->name)) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Fitur User -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <a href="#" id="selectAllUser" class="card-title">User</a>
                                                </div>
                                                <div class="card-body">
                                                    @foreach ($permissions->whereIn('name', ['user.index', 'user.create', 'user.edit', 'user.delete']) as $permission)
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                class="custom-control-input user-checkbox"
                                                                id="user_{{ $permission->id }}"
                                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="user_{{ $permission->id }}">
                                                                {{ ucfirst(str_replace('user.', '', $permission->name)) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Fitur lainnya (loop) -->
                                            @foreach ([
            'employee' => ['employee.index', 'employee.create', 'employee.edit', 'employee.delete'],
            'payroll' => ['payroll.index', 'payroll.export'],
            'recruitment' => ['recruitment.index', 'recruitment.create', 'recruitment.edit', 'recruitment.delete'],
            'attendance' => ['attendance.index', 'attendance.scan'],
            'offrequest' => ['offrequest.index', 'offrequest.create', 'offrequest.approver'],
            'employeebook' => ['employeebook.index', 'employeebook.create', 'employeebook.edit', 'employeebook.delete', 'employeebook.detail'],
            'event' => ['event.index', 'event.lists', 'event.create', 'event.edit', 'event.delete'],
            'overtime' => ['overtime.create', 'overtime.approvals'],
            'settings' => ['settings.index', 'settings.company', 'settings.deductions', 'settings.worksdays'],
            'resignationrequest' => ['resignationrequest.index', 'resignationrequest.create', 'resignationrequest.approver'],
            'submitresign' => ['submitresign.index', 'submitresign.create'],
            'divisions' => ['divisions.index', 'divisions.create', 'divisions.edit', 'divisions.delete'],
        ] as $feature => $featurePermissions)
                                                <div class="card mt-3">
                                                    <div class="card-header">
                                                        <a href="#" id="selectAll{{ ucfirst($feature) }}"
                                                            class="card-title">{{ ucfirst($feature) }}</a>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach ($permissions->whereIn('name', $featurePermissions) as $permission)
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="permissions[]"
                                                                    value="{{ $permission->name }}"
                                                                    class="custom-control-input {{ $feature }}-checkbox"
                                                                    id="{{ $feature }}_{{ $permission->id }}"
                                                                    {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                                <label class="custom-control-label"
                                                                    for="{{ $feature }}_{{ $permission->id }}">
                                                                    {{ ucfirst(str_replace($feature . '.', '', $permission->name)) }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" class="form-control" id="status">
                                                <option value="enable" {{ $role->status == 'enable' ? 'selected' : '' }}>
                                                    Enable
                                                </option>
                                                <option value="disable" {{ $role->status == 'disable' ? 'selected' : '' }}>
                                                    Disable
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" id="saveButton" class="btn btn-primary">Update</button>
                                        <a href="{{ route('role.index') }}" class="btn btn-secondary">Back</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.getElementById('selectAllPermissions').addEventListener('click', function() {
                let checkboxes = document.querySelectorAll('.custom-control-input');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            const features = [
                'dashboard', 'role', 'user', 'employee', 'payroll', 'recruitment', 'attendance',
                'offrequest', 'employeebook', 'event', 'settings', 'overtime', 'divisions', 'resignationrequest',
                'submitresign'
            ];

            features.forEach(feature => {
                document.getElementById(`selectAll${feature.charAt(0).toUpperCase() + feature.slice(1)}`)
                    .addEventListener('click', function(e) {
                        e.preventDefault();
                        let checkboxes = document.querySelectorAll(`.${feature}-checkbox`);
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = !checkbox.checked;
                        });
                    });
            });
        </script>
    </div>
@endsection
