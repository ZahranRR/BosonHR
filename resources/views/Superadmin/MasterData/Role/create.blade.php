@extends('layouts.app')
@section('title', 'Role/create')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Add Role</h3>
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
                            <form action="{{ route('role.store') }}" method="POST" id="quickForm">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="roleName">Role Name</label>
                                        <input type="text" name="name" class="form-control" id="roleName"
                                            value="{{ old('name') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="permissions">Permissions Granted</label>

                                        <!-- Select/Deselect All -->
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="selectAllPermissions">
                                            <label class="custom-control-label" for="selectAllPermissions">Select
                                                All</label>
                                        </div>

                                        <!-- Fitur Dashboard -->
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <a href="#" id="selectAllDashboard" class="card-title">Dashboard</a>
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
                                            <option value="enable">Enable</option>
                                            <option value="disable">Disable</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="button" id="saverole" class="btn btn-primary">Save</button>
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

        document.getElementById('selectAllDashboard').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.dashboard-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        document.getElementById('selectAllRole').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.role-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        document.getElementById('selectAllUser').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur Employee
        document.getElementById('selectAllEmployee').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur Payroll
        document.getElementById('selectAllPayroll').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.payroll-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur Recruitment
        document.getElementById('selectAllRecruitment').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.recruitment-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur Attandance
        document.getElementById('selectAllAttendance').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.attendance-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur Offrequest
        document.getElementById('selectAllOffrequest').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.offrequest-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur EmployeeBook
        document.getElementById('selectAllEmployeebook').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.employeebook-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur Event
        document.getElementById('selectAllEvent').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.event-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur overtime
        document.getElementById('selectAllOvertime').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.overtime-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        // Event listener untuk fitur Settings
        document.getElementById('selectAllSettings').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.settings-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });
        // Event listener untuk fitur Division
        document.getElementById('selectAllDivisions').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.divisions-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        document.getElementById('selectAllResignationrequest').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.resignationrequest-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });

        document.getElementById('selectAllSubmitresign').addEventListener('click', function(e) {
            e.preventDefault();
            let checkboxes = document.querySelectorAll('.submitresign-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        });
    </script>

    <script>
        // Handle the button click for Save
        document.getElementById('saverole').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Menampilkan SweetAlert setelah tombol Save diklik
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                // Submit form setelah alert selesai
                document.getElementById('quickForm').submit(); // Ensure form ID is correct
            });
        });
    </script>
@endsection
