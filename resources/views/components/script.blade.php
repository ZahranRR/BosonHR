<!-- components/script.blade.php -->
<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- OPTIONAL SCRIPTS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('dist/js/pages/dashboard3.js') }}"></script>
<!--  Script for Show/Hide Password -->
<script src="{{ asset('dist/js/password-toggle.js') }}"></script>
<!-- Script Switch alert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<!-- Include JS Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>



{{-- SCRIPT BUAT KLIK EMPLOYEE DETAIL DAN DELETE --}}

<style>
    .clickable-row {
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.clickable-row');
        rows.forEach(row => {
            row.addEventListener('click', function(event) {
                if (!event.target.closest('.deletebutton')) {
                    // Pastikan ini bukan klik pada tombol delete
                    window.location.href = this.dataset.url;
                }
            });
        });
    });

    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("deletebutton")) {
            e.preventDefault(); // Mencegah pengiriman form default
            e.stopPropagation(); // Hentikan event klik pada baris
            const form = e.target.closest("form"); // Ambil form terdekat

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    }).then(() => {
                        form.submit(); // Kirim form setelah konfirmasi
                    });
                }
            });
        }
    });
</script>


{{-- SCRIPT BUAT MODAL IMAGE --}}

<script>
    $('#imageModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var image = button.data('image');
        var modal = $(this);
        modal.find('.modal-body #modalImage').attr('src', image);
    });
</script>


<script>
    function updateValidationStatus(employeeId, status) {
        fetch(`/payroll/validate/${employeeId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                alert('Status berhasil diperbarui: ' + data.message);
            })
            .catch(error => {
                console.error('Ada masalah dengan permintaan:', error);
            });
    }

    {{-- SCRIPT BUAT CREATE EMPLOYE CURRENT SALARY --}}

        <
        script >
        function formatCurrency(input) {
            // Hapus karakter non-digit
            let value = input.value.replace(/\D/g, '');

            // Tambahkan titik sebagai pemisah ribuan
            if (value) {
                input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            } else {
                input.value = ''; // Mengosongkan input jika tidak ada nilai
            }
        }
</script>

{{-- SCRIPT BUAT UPDATE EMPLOYE CURRENT SALARY --}}

<script>
    function formatCurrency(input) {
        // Hapus karakter non-digit
        let value = input.value.replace(/\D/g, '');

        // Tambahkan titik sebagai pemisah ribuan
        if (value) {
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        } else {
            input.value = ''; // Mengosongkan input jika tidak ada nilai
        }
    }
</script>

{{-- SCRIPT BUAT FORM LATE DEDUCTIONS DAN EARLY DEDUCTIONS --}}

<script>
    function formatNumber(input) {
        // Menghapus semua karakter yang bukan angka
        let value = input.value.replace(/[^0-9]/g, '');

        // Format angka dengan titik
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        input.value = value;
    }
</script>


{{-- SCRIPT BUAT FORM SEARCH EMPLOYEE NAME --}}

{{-- @if (Route::is('overtime.create'))
    <script>
        // Data karyawan dari backend
        const employees = @json($employees);

        // Fungsi untuk memfilter karyawan berdasarkan input nama
        function filterEmployees() {
            const query = document.getElementById('employee_name').value.toLowerCase();
            const list = document.getElementById('employee-list');
            list.innerHTML = ''; // Kosongkan list sebelum diisi

            // Filter karyawan berdasarkan nama yang cocok dengan input
            const filteredEmployees = employees.filter(employee => {
                const fullName = `${employee.first_name} ${employee.last_name}`.toLowerCase();
                return fullName.includes(query);
            });

            // Tampilkan hasil pencarianf
            filteredEmployees.forEach(employee => {
                const item = document.createElement('a');
                item.classList.add('list-group-item', 'list-group-item-action');
                item.textContent = `${employee.first_name} ${employee.last_name}`; // Tampilkan nama lengkap
                item.onclick = () => selectEmployee(employee.employee_id,
                    `${employee.first_name} ${employee.last_name}`);
                list.appendChild(item);
            });

            // Tampilkan atau sembunyikan daftar berdasarkan hasil pencarian
            list.style.display = filteredEmployees.length > 0 ? 'block' : 'none';
        }

        // Fungsi untuk memilih nama karyawan dari daftar
        function selectEmployee(id, name) {
            document.getElementById('employee_name').value = name;
            document.getElementById('employee_id').value = id;
            document.getElementById('employee-list').style.display = 'none'; // Sembunyikan daftar setelah memilih
        }

        // Fungsi untuk memastikan input hanya berasal dari daftar karyawan yang ada
        document.getElementById('employee_name').addEventListener('blur', function() {
            const inputName = this.value.toLowerCase();
            const matchedEmployee = employees.find(employee => {
                const fullName = `${employee.first_name} ${employee.last_name}`.toLowerCase();
                return fullName === inputName; // Cocokkan dengan nama lengkap
            });

            if (!matchedEmployee) {
                // Tampilkan alert SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Employee name not found. Please select a name from the list.',
                });

                // Kosongkan input jika tidak ada yang cocok
                this.value = '';
                document.getElementById('employee_id').value = ''; // Kosongkan ID juga
            }
        });

        // Event listener untuk menangani pencarian saat mengetik
        document.getElementById('employee_name').addEventListener('keyup', filterEmployees);
    </script>
@endif --}}


{{-- SCRIPT BUAT DURATION TIME OVERTIME --}}


<script>
    document.getElementById('duration').addEventListener('input', function() {
        const value = this.value;
        const feedback = document.getElementById('duration-feedback');

        // Memastikan bahwa input adalah angka bulat dan dalam rentang 1 hingga 8
        if (!Number.isInteger(Number(value)) || Number(value) < 1 || Number(value) > 8) {
            feedback.style.display = 'block'; // Tampilkan pesan kesalahan
            this.classList.add('is-invalid'); // Tambahkan kelas invalid
        } else {
            feedback.style.display = 'none'; // Sembunyikan pesan kesalahan
            this.classList.remove('is-invalid'); // Hapus kelas invalid
        }
    });
</script>



{{-- SCRIPT UNTUK SEARCH INPUTAN DI TABEL EMPLOYEE --}}

<script>
    // Event listener untuk menangani pencarian saat mengetik
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let query = this.value;

        // Kirimkan request AJAX jika query tidak kosong
        if (query.length > 2 || query === '') {
            fetchEmployees(query);
        }
    });

    // Fungsi untuk mendapatkan dan menampilkan data karyawan berdasarkan query
    function fetchEmployees(query) {
        fetch("{{ route('employee.index') }}?search=" + query, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                let tableBody = document.querySelector('tbody');
                tableBody.innerHTML = ''; // Bersihkan tabel

                data.employees.forEach(employee => {
                    let row = `
                    <tr>
                        <td class="text-left">${employee.first_name}</td>
                        <td class="text-center">${employee.last_name}</td>
                        <td class="text-center">${employee.email}</td>
                        <td class="text-center">${employee.address}</td>
                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm" href="/employee/${employee.employee_id}/edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                            <form method="POST" action="/employee/${employee.employee_id}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => console.log(error));
    }
</script>

{{-- SCRIPT ALERT BUAT SAVE --}}

<script>
    document.getElementById("saveButton").addEventListener("click", function(e) {
        e.preventDefault(); // Mencegah pengiriman form default

        Swal.fire({
            title: "Do you want to save the changes?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            denyButtonText: `Don't save`
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user mengkonfirmasi, submit form
                Swal.fire("Saved!", "", "success").then(() => {
                    document.getElementById("quickForm")
                        .submit(); // Kirim form setelah alert konfirmasi
                });
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    });
</script>

{{-- SCRIPT ALERT BUAT DELETE --}}

<script>
    // Menangani event klik pada tombol delete
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("deleteButton")) {
            e.preventDefault(); // Mencegah pengiriman form default
            const form = e.target.closest("form"); // Ambil form terdekat

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    }).then(() => {
                        form.submit(); // Kirim form setelah konfirmasi
                    });
                }
            });
        }
    });
</script>


{{-- SCRIPT BUAT HURUF PERTAMA KAPITAL SEMUA FORM --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textInputs = document.querySelectorAll('input[type="text"]');

        textInputs.forEach(input => {
            input.addEventListener('input', function() {
                if (input.value.length > 0) {
                    input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
                }
            });
        });
    });
</script>


{{-- SCRIPT BUAT FORM SEARCH EMPLOYEE NAME --}}

{{-- @if (Route::is('overtime.create'))
    <script>
        // Data karyawan dari backend
        const employees = @json($employees);

        // Fungsi untuk memfilter karyawan berdasarkan input nama
        function filterEmployees() {
            const query = document.getElementById('employee_name').value.toLowerCase();
            const list = document.getElementById('employee-list');
            list.innerHTML = ''; // Kosongkan list sebelum diisi

            // Filter karyawan berdasarkan nama yang cocok dengan input
            const filteredEmployees = employees.filter(employee => {
                const fullName = `${employee.first_name} ${employee.last_name}`.toLowerCase();
                return fullName.includes(query);
            });

            // Tampilkan hasil pencarianf
            filteredEmployees.forEach(employee => {
                const item = document.createElement('a');
                item.classList.add('list-group-item', 'list-group-item-action');
                item.textContent = `${employee.first_name} ${employee.last_name}`; // Tampilkan nama lengkap
                item.onclick = () => selectEmployee(employee.employee_id,
                    `${employee.first_name} ${employee.last_name}`);
                list.appendChild(item);
            });

            // Tampilkan atau sembunyikan daftar berdasarkan hasil pencarian
            list.style.display = filteredEmployees.length > 0 ? 'block' : 'none';
        }

        // Fungsi untuk memilih nama karyawan dari daftar
        function selectEmployee(id, name) {
            document.getElementById('employee_name').value = name;
            document.getElementById('employee_id').value = id;
            document.getElementById('employee-list').style.display = 'none'; // Sembunyikan daftar setelah memilih
        }

        // Fungsi untuk memastikan input hanya berasal dari daftar karyawan yang ada
        document.getElementById('employee_name').addEventListener('blur', function() {
            const inputName = this.value.toLowerCase();
            const matchedEmployee = employees.find(employee => {
                const fullName = `${employee.first_name} ${employee.last_name}`.toLowerCase();
                return fullName === inputName; // Cocokkan dengan nama lengkap
            });

            if (!matchedEmployee) {
                // Tampilkan alert SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Employee name not found. Please select a name from the list.',
                });

                // Kosongkan input jika tidak ada yang cocok
                this.value = '';
                document.getElementById('employee_id').value = ''; // Kosongkan ID juga
            }
        });

        // Event listener untuk menangani pencarian saat mengetik
        document.getElementById('employee_name').addEventListener('keyup', filterEmployees);
    </script>
@endif --}}

{{-- SCRIPT BUAT DURATION TIME OVERTIME --}}


<script>
    document.getElementById('duration').addEventListener('input', function() {
        const value = this.value;
        const feedback = document.getElementById('duration-feedback');

        // Memastikan bahwa input adalah angka bulat dan dalam rentang 1 hingga 8
        if (!Number.isInteger(Number(value)) || Number(value) < 1 || Number(value) > 8) {
            feedback.style.display = 'block'; // Tampilkan pesan kesalahan
            this.classList.add('is-invalid'); // Tambahkan kelas invalid
        } else {
            feedback.style.display = 'none'; // Sembunyikan pesan kesalahan
            this.classList.remove('is-invalid'); // Hapus kelas invalid
        }
    });
</script>




{{-- SCRIPT UNTUK SEARCH INPUTAN DI TABEL EMPLOYEE --}}

<script>
    // Event listener untuk menangani pencarian saat mengetik
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let query = this.value;

        // Kirimkan request AJAX jika query tidak kosong
        if (query.length > 2 || query === '') {
            fetchEmployees(query);
        }
    });

    // Fungsi untuk mendapatkan dan menampilkan data karyawan berdasarkan query
    function fetchEmployees(query) {
        fetch("{{ route('employee.index') }}?search=" + query, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                let tableBody = document.querySelector('tbody');
                tableBody.innerHTML = ''; // Bersihkan tabel

                data.employees.forEach(employee => {
                    let row = `
                    <tr>
                        <td class="text-left">${employee.first_name}</td>
                        <td class="text-center">${employee.last_name}</td>
                        <td class="text-center">${employee.email}</td>
                        <td class="text-center">${employee.address}</td>
                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm" href="/employee/${employee.employee_id}/edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                            <form method="POST" action="/employee/${employee.employee_id}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => console.log(error));
    }
</script>





{{-- SCRIPT UNTUK PAYROLL --}}

<script>
    function validatePayroll(payrollId) {
        fetch(`/payroll/${payrollId}/validate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => {
            if (response.ok) location.reload();
        });
    }
