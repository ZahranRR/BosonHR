@extends('layouts.app')
@section('title', 'Overtime/index')
@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Overtime Request List</h3>
                <div class="card-tools">
                    <!-- Tombol untuk menambah pengajuan overtime -->
                    <a class="btn btn-primary btn-sm" href="{{ route('overtime.create') }}">
                        <i class="fas fa-plus"></i> Add
                    </a>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Tabel untuk daftar permohonan overtime -->
            <div class="table-responsive">
                <table class="table table-striped projects">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Overtime Date</th>
                            <th>Duration (Hours)</th>
                            <th>Notes</th>
                            <th>Current Salary</th>
                            <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($overtimes as $overtime)
                        
                                <tr>
                                    <td>{{ $overtime->employee->first_name }} {{ $overtime->employee->last_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d-m-Y') }}</td>
                                    <td>{{ $overtime->duration }} hours</td>
                                    <td>{{ $overtime->notes }}</td>
                                    <td class="text-center">
                                        @if($overtime->employee && $overtime->employee->current_salary)
                                            {{ number_format($overtime->employee->current_salary, 0, ',', '.') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    
                                    
                                    <td>
                                        <span class="badge {{ $overtime->status == 'approved' ? 'bg-success' : ($overtime->status == 'rejected' ? 'bg-danger' : 'bg-secondary') }}">
                                            {{ ucfirst($overtime->status) }}
                                        </span>
                                    </td>
                               
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">There are no overtime requests available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>
        </div>
    </section>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        @endif
    </script>

@endsection
