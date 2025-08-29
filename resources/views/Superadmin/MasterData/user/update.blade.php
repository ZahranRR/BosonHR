@extends('layouts.app')
@section('title', 'Datauser/edit')
@section('content')
    <div class="content">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Edit User</h3>
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
                    <form id="editUserForm" action="{{ route('datauser.update', $user->user_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $user->name) }}">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', $user->email) }}">
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-control" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control">
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control">
                                    <span class="input-group-text" id="togglePasswordConfirmation">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" id="updateButton" class="btn btn-primary">Update</button>
                            <a href="{{ route('datauser.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.getElementById('updateButton').addEventListener('click', function(e) {
            Swal.fire({
                title: "Do you want to save the changes?",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Save",
                denyButtonText: `Don't save`
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('editUserForm').submit();
                    Swal.fire("Saved!", "", "success");
                } else if (result.isDenied) {
                    Swal.fire("Changes are not saved", "", "info");
                }
            });
        });
    </script>
@endsection
