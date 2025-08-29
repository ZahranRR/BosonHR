<!-- View untuk halaman Create Submit Resignation -->
@extends('layouts.app')
@section('title', 'Submit Resignation/Create')
@section('content')
    <div class="container-fluid">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Submit Resignation Request</h3>
                    </div>

                    <form method="POST" action="{{ route('submitresign.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="employee_name">Employee Name</label>
                                <input type="text" id="employee_name" class="form-control"
                                    placeholder="Type to search employee" onkeyup="filterEmployees()" required>
                                <input type="hidden" name="employee_id" id="employee_id" required>
                                <div id="employee-list" class="list-group" style="display: none;"></div>
                                <!-- Dropdown hasil pencarian -->
                            </div>


                            <div class="form-group">
                                <label for="resign_date">Resign Date</label>
                                <input type="date" name="resign_date" id="resign_date" class="form-control"
                                    min="{{ now()->toDateString() }}" required>
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason</label>
                                <textarea name="reason" id="reason" class="form-control" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('submitresign.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <script>
        function filterEmployees() {
            let query = document.getElementById("employee_name").value;

            // Jika panjang query >= 2, lakukan pencarian
            if (query.length >= 2) {
                fetch(`/employee/search?query=${query}`)

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
    </script>
@endsection
