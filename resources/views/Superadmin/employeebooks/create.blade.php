@extends('layouts.app')
@section('title', 'Employeebooks/create')
@section('content')
    <div class="content">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary w-100">
                    <div class="card-header">
                        <h3 class="card-title">Add {{ ucfirst($category) }}</h3>
                    </div>
                    <form id="quickForm" action="{{ route('employeebooks.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="category" value="{{ $category }}">
                        <div class="card-body">
                            <!-- Employee Search -->
                            <div class="form-group">
                                <label for="employee_name">Employee Name</label>
                                <input type="text" id="employee_name" class="form-control"
                                    placeholder="Type to search employee" onkeyup="filterEmployees()" required>
                                <input type="hidden" name="employee_id" id="employee_id" required>
                                <div id="employee-list" class="list-group" style="display: none;"></div>
                                <!-- Dropdown hasil pencarian -->
                            </div>

                            <!-- Type of -->
                            <div class="form-group">
                                <label for="type_of">Type of</label>
                                <select name="type_of" id="type_of" class="form-control">
                                    <option value="SOP">SOP</option>
                                    <option value="Administrative">Administrative</option>
                                    <option value="Behavior">Behavior</option>
                                </select>
                            </div>

                            <!-- Incident Date -->
                            <div class="form-group">
                                <label for="incident_date">Incident Date</label>
                                <input type="date" name="incident_date" id="incident_date" class="form-control" required>
                            </div>

                            <!-- Incident Detail -->
                            <div class="form-group">
                                <label for="incident_detail">Incident Detail</label>
                                <textarea name="incident_detail" id="incident_detail" class="form-control" required></textarea>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" required></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer">
                            <button type="button" id="savebooks" class="btn btn-primary">Save</button>
                            <a href="{{ route('employeebooks.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <!-- Employee Search Script -->
    <script>
        // Fungsi untuk memfilter karyawan berdasarkan input nama
        function filterEmployees() {
            let query = document.getElementById("employee_name").value;

            // Jika panjang query >= 2, lakukan pencarian
            if (query.length >= 2) {
                fetch(`/Superadmin/employeebooks/search/employees?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        let employeeList = document.getElementById("employee-list");
                        employeeList.innerHTML = ''; // Hapus hasil pencarian sebelumnya

                        // Jika ada hasil pencarian
                        if (data.length > 0) {
                            data.forEach(employee => {
                                let listItem = document.createElement("div");
                                listItem.classList.add("list-group-item");
                                listItem.textContent = employee.full_name;

                                // Klik pada item untuk memilih karyawan
                                listItem.onclick = function() {
                                    document.getElementById("employee_name").value = employee.full_name;
                                    document.getElementById("employee_id").value = employee.employee_id;
                                    employeeList.style.display =
                                        "none"; // Sembunyikan daftar setelah memilih
                                };

                                employeeList.appendChild(listItem);
                            });
                            employeeList.style.display = "block"; // Tampilkan daftar
                        } else {
                            employeeList.style.display = "none"; // Jika tidak ada hasil, sembunyikan daftar
                        }
                    })
                    .catch(error => console.error('Error fetching employees:', error));
            } else {
                document.getElementById("employee-list").style.display = "none"; // Sembunyikan jika query terlalu pendek
            }
        }

        // Event listener untuk tombol simpan
        document.getElementById('savebooks').addEventListener('click', function(event) {
            event.preventDefault();
            let employeeId = document.getElementById('employee_id').value;

            // Validasi apakah employee_id terisi
            if (!employeeId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a valid employee from the list.'
                });
                return;
            }

            // Jika valid, simpan form
            Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                document.getElementById('quickForm').submit(); // Submit form
            });
        });
    </script>

@endsection
