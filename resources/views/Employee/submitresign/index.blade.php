@extends('layouts.app')
@section('title', 'Submit Resignation/Index')
@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h3 class="card-title mb-0">Submit Resignation Requests</h3>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('submitresign.create') }}" class="btn btn-primary" title="Create Resignation">
                            <i class="fas fa-plus"></i> Add
                        </a>

                        <form action="{{ route('submitresign.index') }}" method="GET" class="form-inline ml-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by employee name..."
                                value="{{ request()->query('search') }}">
                            <button type="submit" class="btn btn-secondary ml-2">Search</button>
                        </form>
                    </div>
                </div>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th class="text-left" style="width: 20%">Employee Name</th>
                                <th class="text-center" style="width: 20%">Resignation Date</th>
                                <th class="text-center" style="width: 20%">Reason</th>
                                <th class="text-center" style="width: 20%">Remarks</th>
                                <th class="text-right" style="width: 20%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resignationRequests as $request)
                                <tr>
                                    <td class="text-left">
                                        {{ $request->employee->first_name }} {{ $request->employee->last_name }}
                                    </td>
                                    <td class="text-center">{{ $request->resign_date }}</td>
                                    <td class="text-center">{{ $request->reason }}</td>
                                    <td class="text-center">{{ $request->remarks }}</td>
                                    <td class="text-right">
                                        <span class="badge 
                                            @if($request->status == 'pending') badge-warning
                                            @elseif($request->status == 'approved') badge-success
                                            @elseif($request->status == 'rejected') badge-danger
                                            @else badge-secondary
                                            @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="pagination-container">
                        {{ $resignationRequests->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
