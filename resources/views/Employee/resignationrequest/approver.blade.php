@extends('layouts.app')
@section('title', 'Resignation Requests/Approval')
@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Resignation Requests Approval</h4>
            </div>
            <div class="card-body">
                @if ($role === 'manager')
                    <div class="card mb-4">
                        <h5 class="card-header bg-warning">Pending Requests</h5>
                        <div class="card-body">
                            @if ($resignationsPending->isEmpty())
                                <div class="alert alert-info">
                                    There are no pending resignation requests.
                                </div>
                            @else
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Resign Date</th>
                                            <th>Reason</th>
                                            <th>Remarks</th>
                                            <th>Document</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($resignationsPending as $request)
                                            <tr>
                                                <td>{{ $request->name }}</td>
                                                <td>{{ $request->resign_date->format('d M Y') }}</td>
                                                <td>{{ $request->reason }}</td>
                                                <td>{{ $request->remarks ?? '-' }}</td>
                                                <td>
                                                    @if ($request->document)
                                                        <a href="{{ asset('storage/' . $request->document) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <form
                                                        action="{{ route('resignationrequest.updateStatus', $request->resignationrequest_id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button class="btn btn-success btn-sm">Approve</button>
                                                    </form>
                                                    <form
                                                        action="{{ route('resignationrequest.updateStatus', $request->resignationrequest_id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card untuk Processed Resignation Requests -->
        <div class="card mt-4">
            <h5 class="card-header bg-secondary text-white">Processed Requests</h5>
            <div class="card-body">
                @if ($resignationsProcessed->isEmpty())
                    <div class="alert alert-info">
                        No processed resignation requests.
                    </div>
                @else
                    @foreach ($resignationsProcessed as $request)
                        <div class="resignation-request border-bottom pb-3 mb-3">
                            <p><strong>Name:</strong> {{ $request->name }}</p>
                            <p><strong>Resign Date:</strong> {{ $request->resign_date->format('Y-m-d') }}</p>
                            <p><strong>Reason:</strong> {{ $request->reason }}</p>
                            <p><strong>Remarks:</strong> {{ $request->remarks ?? 'N/A' }}</p>
                            <p>
                                <strong>Document:</strong>
                                @if ($request->document)
                                    <a href="{{ asset('storage/' . $request->document) }}" target="_blank">View
                                        Document</a>
                                @else
                                    N/A
                                @endif
                            </p>
                            <p>
                                <strong>Status:</strong>
                                <span class="badge {{ $request->status == 'approved' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
@endsection
