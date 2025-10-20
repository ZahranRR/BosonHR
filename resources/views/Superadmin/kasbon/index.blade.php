@extends('layouts.app')
@section('title', 'Kasbon Index')

@section('content')
<section class="content">

    {{-- Modal Kasbon
    <div class="modal fade" id="kasbonModal" tabindex="-1" aria-labelledby="kasbonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah / Edit Kasbon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="kasbonForm">
                        @csrf
                        <input type="hidden" id="kasbonPayrollId" name="payroll_id">

                        <div class="mb-3">
                            <label for="kasbonNominal" class="form-label">Nominal:</label>
                            <input type="number" class="form-control" id="kasbonNominal" name="cash_advance"
                                placeholder="Masukkan nominal" min="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCashAdvance">Save Changes</button>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Card Kasbon --}}
    <div class="card">
        <div class="card-header">
        <div class="d-flex justify-content-between w-100 align-items-center">
                    <h3 class="card-title mb-0">Data Kasbon</h3>

                    <div class="d-flex align-items-center">
                        <a href="{{ route('kasbon.create') }}" class="btn btn-primary" title="Create Employee">
                            <i class="fas fa-plus"></i> Add
                        </a>

                        <form action="{{ route('kasbon.index') }}" method="GET" class="form-inline ml-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by name..."
                                value="{{ request()->query('search') }}" >
                            <button type="submit" class="btn btn-secondary ml-2">Search</button>
                        </form>
                    </div>
                </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped projects">
                    <thead>
                        <tr>
                            <th style="width: 10%">Employee Name</th>
                            <th style="width: 10%" class="text-center">Current Salary</th>
                            <th style="width: 10%" class="text-center">Total Amount</th>
                            <th style="width: 10%" class="text-center">Installments</th>
                            <th style="width: 10%" class="text-center">Per Installments</th>
                            <th style="width: 10%" class="text-center">Remaining</th>
                            <th style="width: 10%" class="text-center">Start Month</th>
                            <th style="width: 10%" class="text-center">Status</th>
                            {{-- <th style="width: 15%" class="text-center">Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kasbon as $c)
                        <tr>
                            <td>{{ $c->employee->first_name }} {{ $c->employee->last_name }}</td>
                            <td class="text-center">Rp. {{ number_format($c->employee->current_salary, 0, ',', '.') }}</td>
                            <td class="text-center">Rp. {{ number_format($c->total_amount, 0, ',', '.') }}</td>
                            <td class="text-center">{{$c->installments}}</td>
                            <td class="text-center">Rp. {{ number_format($c->installment_amount, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($c->remaining_installments, 0, ',', '.') }}</td>
                            <td class="text-center">{{$c->start_month}}</td>
                            <td class="text-center">
                                <span class="badge
                                    @if($c->status =='ongoing') bg-warning
                                    @elseif ($c->status == 'completed') bg-success
                                    @else bg-danger @endif">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data kasbon.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // saat tombol diklik isi modal
        $('#kasbonModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const payrollId = button.data('id');
            const cashAdvance = button.data('cash-advance');

            $('#kasbonPayrollId').val(payrollId);
            $('#kasbonNominal').val(cashAdvance);
        });

        // saat save
        $('#saveCashAdvance').on('click', function() {
            const payrollId = $('#kasbonPayrollId').val();
            const cashAdvance = $('#kasbonNominal').val();

            if (!payrollId || !cashAdvance) {
                Swal.fire('Error', 'Payroll ID dan nominal harus diisi.', 'error');
                return;
            }

            $.ajax({
                url: `/Superadmin/kasbon/cash-advance/${payrollId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    payroll_id: payrollId,
                    cash_advance: cashAdvance
                },
                success: function(response) {
                    $('#kasbonModal').modal('hide');
                    $('#cash-advance-' + payrollId).html('Rp. ' + response.cash_advance);
                    Swal.fire('Berhasil', response.message, 'success');
                },
                error: function(xhr) {
                    Swal.fire('Gagal', 'Gagal menyimpan data kasbon.', 'error');
                    console.error(xhr.responseJSON);
                }
            });
        });
    });
</script>
@endsection



