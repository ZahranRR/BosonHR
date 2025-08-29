@extends('layouts.app')
@section('title', 'Resignation Requests/Index')
@section('content')
    <div class="container mt-4">
        <!-- Card Header -->
        <div class="card">
            <div class="card-header">
                <h4>Resignation Requests</h4>
            </div>
            <div class="card-body">
                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Button Create Resignation Request -->
                @if ($role === 'Manager' || $role === 'Superadmin' || $role === 'Employee')
                    @can('resignationrequest.create')
                        @if ($resignationsByUser->isEmpty() || $resignationsByUser->last()->status === 'rejected')
                            <div class="text-center mt-3">
                                <a href="{{ route('resignationrequest.create') }}" class="btn btn-primary">
                                    Create Resignation Request
                                </a>
                            </div>
                        @endif
                    @endcan
                @endif

                <!-- Manager's Own Requests -->
                @if (!$resignationsByUser->isEmpty())
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Your Resignation Requests</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Resign Date</th>
                                        <th>Reason</th>
                                        <th>Remarks</th>
                                        <th>Document</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resignationsByUser as $request)
                                        <tr>
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
                                                <span
                                                    class="badge {{ $request->status === 'approved' ? 'bg-success' : ($request->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Pending Requests -->
                @if (!$resignationsPending->isEmpty())
                    <div class="card mt-4">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0">Pending Requests</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Resign Date</th>
                                        <th>Reason</th>
                                        <th>Remarks</th>
                                        <th>Document</th>
                                        <th>Status</th>
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
                                                <span class="badge bg-warning">Pending</span>
                                            </td>
                                            <td>
                                                <form
                                                    action="{{ route('resignationrequest.updateStatus', $request->resignationrequest_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" name="status" value="approved"
                                                        class="btn btn-success btn-sm">
                                                        Approve
                                                    </button>
                                                    <button type="submit" name="status" value="rejected"
                                                        class="btn btn-danger btn-sm">
                                                        Reject
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Processed Requests -->
                @if (!$resignationsProcessed->isEmpty())
                    <div class="card mt-4">
                        <div class="card-header bg-success">
                            <h5 class="mb-0">Processed Requests</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Resign Date</th>
                                        <th>Reason</th>
                                        <th>Remarks</th>
                                        <th>Document</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resignationsProcessed as $request)
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
                                                <span
                                                    class="badge {{ $request->status === 'approved' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Employee Requests -->
                @if ($role === 'employee')
                    @can('resignationrequest.create')
                        @if ($resignations->isEmpty() || $resignations->last()->status === 'rejected')
                            <div class="text-center mt-3">
                                <a href="{{ route('resignationrequest.create') }}" class="btn btn-primary">
                                    Create Resignation Request
                                </a>
                            </div>
                        @endif
                    @endcan
                @endif

                @if (!$resignations->isEmpty())
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Your Resignation Requests</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Resign Date</th>
                                        <th>Reason</th>
                                        <th>Remarks</th>
                                        <th>Document</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resignations as $request)
                                        <tr>
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
                                                <span
                                                    class="badge {{ $request->status === 'approved' ? 'bg-success' : ($request->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
