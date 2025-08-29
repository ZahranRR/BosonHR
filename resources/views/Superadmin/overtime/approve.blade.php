@extends('layouts.app')

@section('content')
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Overtime List</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: "{{ session('success') }}",
                        timer: 3000,
                        showConfirmButton: false
                    });
                </script>
            @endif

            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "{{ session('error') }}",
                        timer: 3000,
                        showConfirmButton: false
                    });
                </script>
            @endif

            <!-- Daftar Overtime yang Belum Disetujui -->
            <h4>Overtime Applications Pending Approval</h4>
            @if ($pendingOvertimes->isEmpty())
                <div class="alert alert-info" role="alert">
                    No overtime applications need to be approved at this time.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Overtime Date</th>
                                <th>Duration (Hours)</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingOvertimes as $overtime)
                                <tr>
                                    <td>{{ $overtime->employee->user->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d-m-Y') }}</td>
                                    <td>{{ $overtime->duration }}</td>
                                    <td>{{ $overtime->notes }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ ucfirst($overtime->status) }}</span>
                                    </td>
                                    <td class="d-flex">
                                        @can('overtime.approvals')
                                            <!-- Formulir Approve -->
                                            <form action="{{ route('overtime.approve', $overtime->id) }}" method="POST" class="mr-2">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <!-- Formulir Reject -->
                                            <form action="{{ route('overtime.reject', $overtime->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Daftar History Overtime yang Disetujui/Ditolak oleh Manager yang Sedang Login -->
            <h4 class="mt-5">Overtime History (Approved & Rejected by You)</h4>
            @if ($historyOvertimes->isEmpty())
                <div class="alert alert-info" role="alert">
                    No overtime history found for your approval.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Overtime Date</th>
                                <th>Duration (Hours)</th>
                                <th>Notes</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historyOvertimes as $overtime)
                                <tr>
                                    <td>{{ $overtime->employee->first_name }} {{ $overtime->employee->last_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d-m-Y') }}</td>
                                    <td>{{ $overtime->duration }}</td>
                                    <td>{{ $overtime->notes }}</td>
                                    <td>
                                        <span class="badge 
                                            @if ($overtime->status == 'approved') bg-success
                                            @elseif ($overtime->status == 'rejected') bg-danger
                                            @else bg-warning @endif">
                                            {{ ucfirst($overtime->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>


    @push('scripts')
        <script>
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

        </script>
    @endpush
@endsection
