@extends('layouts.app')
@section('title', 'Attendance/index')
@section('content')
    <style>
        .modal-body img {
            max-height: 80vh;
            max-width: 100%;
            object-fit: contain;
        }
    </style>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Attendance</h3>
                <div class="card-tools">
                    <form method="GET" action="{{ route('attandance.index') }}" class="form-inline">
                        <div class="form-group mr-2">
                            <input type="date" name="date" class="form-control" value="{{ $date }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th class="text-left">Name</th>
                                <th class="text-center">Check-In</th>
                                <th class="text-center">Photo</th>
                                <th class="text-center">Check-Out</th>
                                <th class="text-center">Photo</th>
                                <th class="text-center">Status Check-In</th>
                                <th class="text-center">Status Check-Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $attendance)
                                <tr>
                                    <td class="text-left">
                                        <a
                                            href="{{ route('attendance.recap', ['employee_id' => $attendance->employee->employee_id, 'month' => now()->format('Y-m')]) }}">
                                            {{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        {{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($attendance->image_checkin)
                                            <a href="#" data-toggle="modal" data-target="#imageModal"
                                                data-image="{{ asset('storage/' . $attendance->image_checkin) }}">
                                                <img src="{{ asset('storage/' . $attendance->image_checkin) }}"
                                                    alt="Check-In Image" width="100">
                                            </a>
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $attendance->check_out ? $attendance->check_out->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($attendance->image_checkout)
                                            <a href="#" data-toggle="modal" data-target="#imageModal"
                                                data-image="{{ asset('storage/' . $attendance->image_checkout) }}">
                                                <img src="{{ asset('storage/' . $attendance->image_checkout) }}"
                                                    alt="Check-Out Image" width="100">
                                            </a>
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge 
            {{ $attendance->check_in_status === 'IN' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $attendance->check_in_status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge 
        {{ $attendance->check_out_status === 'OUT' || $attendance->check_out_status === 'LATE' || $attendance->check_out_status === 'EARLY' ? 'badge-danger' : 'badge-success' }}">
                                            {{ $attendance->check_out_status }}
                                        </span>
                                    </td>


                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No data for the selected date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="pagination-container">
                        {{ $attendances->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Attendance Image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" alt="Attendance Image" class="img-fluid" id="modalImage">
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#imageModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var imageUrl = button.data('image');
                    console.log('Image URL:', imageUrl);
                    var modal = $(this);
                    modal.find('#modalImage').attr('src', imageUrl);
                });
            });
        </script>

    </section>
@endsection
