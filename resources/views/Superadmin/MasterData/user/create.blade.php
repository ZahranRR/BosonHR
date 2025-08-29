@extends('layouts.app')
@section('title', 'Datauser/create')
@section('content')
    <div class="content">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Add User</h3>
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
                    <form action="{{ route('datauser.store') }}" method="POST" id="userForm">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-control" value="{{ old('role') }}" required>
                                    @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" required>
                                    <span class="input-group-text" id="togglePasswordConfirmation">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                            <a href="{{ route('datauser.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>


    <script>
        document.getElementById('saveBtn').addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                // Submit form setelah alert selesai
                document.getElementById('userForm').submit();
            });
        });
    </script>
@endsection
