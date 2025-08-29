@extends('layouts.app')
@section('title', 'Recruitment/index')
@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recruitment</h3>
                <div class="card-tools d-flex align-items-center">
                    <!-- Button to add a new recruitment -->
                    <a class="btn btn-primary btn-sm" href="{{ route('recruitment.create') }}">
                        <i class="fas fa-plus"></i> Add
                    </a>

                    <!-- Search form with margin to the right -->
                    <form action="{{ route('recruitment.index') }}" method="GET" class="form-inline ml-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, email..."
                            value="{{ request()->query('search') }}"> <!-- Keep the search value in the input field -->
                        <button type="submit" class="btn btn-secondary ml-2">Search</button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th class="text-left">First Name</th>
                                <th class="text-center">Last Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Phone Number</th>
                                <th class="text-center">Apply Position</th>
                                <th class="text-center">CV File</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($recruitments->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center">No results found</td>
                                </tr>
                            @else
                                @foreach ($recruitments as $recruitment)
                                    <tr data-url="{{ route('recruitment.show', $recruitment->recruitment_id) }}"
                                        class="clickable-row">
                                        <td class="text-left">{{ $recruitment->first_name }}</td>
                                        <td class="text-center">{{ $recruitment->last_name }}</td>
                                        <td class="text-center">{{ $recruitment->email }}</td>
                                        <td class="text-center">{{ $recruitment->phone_number }}</td>
                                        <td class="text-center">{{ $recruitment->apply_position }}</td>
                                        <td class="text-center">
                                            <a href="{{ Storage::url($recruitment->cv_file) }}" target="_blank"
                                                onclick="event.stopPropagation();">Download CV</a>
                                        </td>
                                        <td class="project-state text-center">
                                            <span
                                                class="badge badge-{{ $recruitment->status == 'Accept' ? 'success' : ($recruitment->status == 'Decline' ? 'danger' : 'light') }}">
                                                {{ $recruitment->status }}
                                            </span>
                                        </td>
                                        <td class="project-actions text-right">
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('recruitment.edit', $recruitment->recruitment_id) }}">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </a>
                                            <form id="quickForm" action="{{ route('recruitment.destroy', $recruitment->recruitment_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="deletebutton btn btn-sm btn-danger">
                                                    <i class="deletebutton fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="pagination-container">
                        {{ $recruitments->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
