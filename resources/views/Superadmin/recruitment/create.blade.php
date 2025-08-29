@extends('layouts.app')
@section('title', 'Recruitment/create')
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
                        <h3 class="card-title">Add Recruitment</h3>
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

                    <!-- Form utama -->
                    <form id="quickForm" action="{{ route('recruitment.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required
                                    value="{{ old('first_name') }}">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" required
                                    value="{{ old('last_name') }}">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+62</span>
                                    </div>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control"
                                        value="{{ old('phone_number') }}" required oninput="updatePhoneNumber(this)"
                                        onkeypress="validatePhoneNumber(event)">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                    value="{{ old('date_of_birth') }}">
                            </div>

                            <div class="form-group">
                                <label for="last_education">Last Education</label>
                                <select name="last_education" id="last_education" class="form-control" required>
                                    <option value="Elementary School">Elementary School</option>
                                    <option value="Junior High School">Junior High School</option>
                                    <option value="Senior High School">Senior High School</option>
                                    <option value="Vocational High School">Vocational High School</option>
                                    <option value="Associate Degree 1">Associate Degree 1</option>
                                    <option value="Associate Degree 2">Associate Degree 2</option>
                                    <option value="Associate Degree 3">Associate Degree 3</option>
                                    <option value="Bachelor’s Degree">Bachelor’s Degree</option>
                                    <option value="Master’s Degree">Master’s Degree</option>
                                    <option value="Doctoral Degree">Doctoral Degree</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="last_position">Last Position</label>
                                <input type="text" name="last_position" id="last_position" class="form-control"
                                    value="{{ old('last_position') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="apply_position">Apply Position</label>
                                <input type="text" name="apply_position" id="apply_position" class="form-control"
                                    value="{{ old('apply_position') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="cv_file">CV File</label>
                                <input type="file" name="cv_file" id="cv_file" class="form-control"
                                    value="{{ old('cv_file') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Initial Interview">Initial Interview</option>
                                    <option value="User Interview 1">User Interview 1</option>
                                    <option value="User Interview 2">User Interview 2</option>
                                    <option value="Background Check">Background Check</option>
                                    <option value="Offering letter">Offering letter</option>
                                    <option value="Accept">Accept</option>
                                    <option value="Decline">Decline</option>
                                </select>
                            </div>

                            <div class="card-footer">
                                <button type="submit" id="saverecruitment" class="btn btn-primary">Save</button>
                                <a href="{{ route('recruitment.index') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.getElementById('saverecruitment').addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah pengiriman form langsung
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                document.getElementById('quickForm').submit(); // Kirim form setelah alert sukses
            });
        });
        // Function to automatically update phone number prefix
        function updatePhoneNumber(input) {
            // Get the value from the input
            let value = input.value;

            // If the value starts with '0', replace it with '+62'
            if (value.startsWith('0')) {
                input.value = '+62' + value.slice(1);
            }
        }

        // Function to validate phone number input (only numbers allowed)
        function validatePhoneNumber(event) {
            const input = event.target;
            const char = String.fromCharCode(event.which);

            // Allow only numbers (0-9) and prevent other characters
            if (!/[0-9]/.test(char)) {
                event.preventDefault(); // Prevent the input
                document.getElementById('phoneNumberWarning').style.display = 'block'; // Show warning
            } else {
                document.getElementById('phoneNumberWarning').style.display = 'none'; // Hide warning
            }
        }
    </script>
@endsection
