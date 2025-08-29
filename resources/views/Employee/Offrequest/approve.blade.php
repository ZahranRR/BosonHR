@extends('layouts.app')
@section('title', 'Offrequest/approval')
@section('content')
    <style>
        body {
            background-color: white !important;
        }

        .content {
            background-color: white !important;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>

    <section class="content">
        <div>
            @if ($offrequests->count() > 0)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>📢Attention!</strong> There are <strong>{{ $offrequests->count() }}</strong> pending leave
                    requests that need your approval.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


            <div class="card-header">
                <h3 class="card-title">Off Request Approval</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('offrequest.approver') }}">
                    <div class="row mb-4">
                        <div class="col-md-9 col-sm-8">
                            <label for="filter_date">Filter by Date:</label>
                            <input type="date" name="filter_date" id="filter_date" class="form-control"
                                value="{{ request('filter_date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 col-sm-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block w-100">Filter</button>
                        </div>
                    </div>
                </form>
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#pending" role="tab" data-toggle="tab">Off Request List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#history" role="tab" data-toggle="tab">Leave Application History</a>
                    </li>
                </ul>

                <!-- Tab Panes -->
                <div class="tab-content mt-4">
                    <div role="tabpanel" class="tab-pane active" id="pending">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Start Event</th>
                                        <th>End Event</th>
                                        <th>Status</th>
                                        <th>Picture Proof</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($offrequests as $offrequest)
                                        <tr>
                                            <td>{{ $offrequest->user->name ?? 'N/A' }}</td>
                                            <td>{{ $offrequest->user->email ?? 'N/A' }}</td>
                                            <td>{{ $offrequest->title }}</td>
                                            <td>{{ $offrequest->description }}</td>
                                            <td>{{ $offrequest->start_event ? $offrequest->start_event->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td>{{ $offrequest->end_event ? $offrequest->end_event->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td>{{ ucfirst($offrequest->status) }}</td>
                                            <td>
                                                @if ($offrequest->image)
                                                    <img src="{{ asset('uploads/' . $offrequest->image) }}" alt="Proof"
                                                        style="max-width: 100px; cursor: pointer;" data-toggle="modal"
                                                        data-target="#imageModal-{{ $offrequest->offrequest_id }}">

                                                    <!-- Modal -->
                                                    <div class="modal fade"
                                                        id="imageModal-{{ $offrequest->offrequest_id }}" tabindex="-1"
                                                        role="dialog"
                                                        aria-labelledby="imageModalLabel-{{ $offrequest->offrequest_id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="imageModalLabel-{{ $offrequest->offrequest_id }}">
                                                                        Proof Image</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img src="{{ asset('uploads/' . $offrequest->image) }}"
                                                                        alt="Proof" class="img-fluid rounded">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>

                                            <td class="d-flex">
                                                @can('offrequest.approver')
                                                    <form id="approveForm-{{ $offrequest->offrequest_id }}" method="POST"
                                                        action="{{ route('offrequest.approve', $offrequest->offrequest_id) }}"
                                                        class="mr-2">
                                                        @csrf
                                                        <button type="button" class="btn btn-success btn-sm approve-button"
                                                            data-id="{{ $offrequest->offrequest_id }}">Approve</button>
                                                    </form>
                                                    <form id="rejectForm-{{ $offrequest->offrequest_id }}" method="POST"
                                                        action="{{ route('offrequest.reject', $offrequest->offrequest_id) }}">
                                                        @csrf
                                                        <button type="button" class="btn btn-danger btn-sm reject-button"
                                                            data-id="{{ $offrequest->offrequest_id }}">Reject</button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No pending off requests</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="history">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Start Event</th>
                                        <th>End Event</th>
                                        <th>Status</th>
                                        <th>Approved By</th>
                                        <th>Picture Proof</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($approvedRequests as $approvedRequest)
                                        <tr>
                                            <td>{{ $approvedRequest->user->name ?? 'N/A' }}</td>
                                            <td>{{ $approvedRequest->user->email ?? 'N/A' }}</td>
                                            <td>{{ $approvedRequest->title }}</td>
                                            <td>{{ $approvedRequest->description }}</td>
                                            <td>{{ $approvedRequest->start_event ? $approvedRequest->start_event->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td>{{ $approvedRequest->end_event ? $approvedRequest->end_event->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $approvedRequest->status == 'approved' ? 'bg-success' : ($approvedRequest->status == 'rejected' ? 'bg-danger' : 'bg-secondary') }}">
                                                    {{ ucfirst($approvedRequest->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $approvedRequest->approved_by ?? 'N/A' }}</td>
                                            <td>
                                                @if ($approvedRequest->image)
                                                    <img src="{{ asset('uploads/' . $approvedRequest->image) }}"
                                                        alt="Proof" style="max-width: 100px; cursor: pointer;"
                                                        data-toggle="modal"
                                                        data-target="#imageModal-{{ $approvedRequest->offrequest_id }}">
                                                    <div class="modal fade"
                                                        id="imageModal-{{ $approvedRequest->offrequest_id }}"
                                                        tabindex="-1" role="dialog"
                                                        aria-labelledby="imageModalLabel-{{ $approvedRequest->offrequest_id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="imageModalLabel-{{ $approvedRequest->offrequest_id }}">
                                                                        Proof Image</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img src="{{ asset('uploads/' . $approvedRequest->image) }}"
                                                                        alt="Proof" class="img-fluid rounded">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No leave application history found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Approve Button Click
            document.querySelectorAll('.approve-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    const formId = 'approveForm-' + button.getAttribute('data-id');
                    Swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: 'Leave request has been approved!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        document.getElementById(formId).submit();
                    });
                });
            });

            // Handle Reject Button Click
            document.querySelectorAll('.reject-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    const formId = 'rejectForm-' + button.getAttribute('data-id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, reject it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire(
                                'Rejected!',
                                'The leave request has been rejected.',
                                'error'
                            ).then(() => {
                                document.getElementById(formId).submit();
                            });
                        }
                    });
                });
            });
        });
    </script>


@endsection
