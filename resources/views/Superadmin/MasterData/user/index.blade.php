@extends('layouts.app')
@section('title', 'Datauser/index')
@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h3 class="card-title mb-0">Datauser</h3>

                    <div class="d-flex align-items-center">
                        <a href="{{ route('datauser.create') }}" class="btn btn-primary" title="Create Employee">
                            <i class="fas fa-plus"></i> Add
                        </a>

                        <form action="{{ route('datauser.index') }}" method="GET" class="form-inline ml-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email..."
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
            @if (session('errors'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let errorMessages = '';
                        @foreach (session('errors')->all() as $error)
                            errorMessages += '{{ $error }}\n';
                        @endforeach

                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: errorMessages,
                        });
                    });
                </script>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th class="text-left" style="width: 20%">Name</th>
                                <th class="text-center" style="width: 20%">Email</th>
                                <th class="text-center" style="width: 20%">Role</th>
                                <th class="text-right" style="width: 20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-left">{{ $user->name }}</td>
                                    <td class="text-center">{{ $user->email }}</td>
                                    <td class="text-center">
                                        @if ($user->roles->isNotEmpty())
                                            {{ $user->roles->pluck('name')->implode(', ') }}
                                        @else
                                            <span class="text-muted">No Role Assigned</span>
                                        @endif
                                    </td>
                                    <td class="project-actions text-right">
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('datauser.edit', ['user_id' => $user->user_id]) }}">
                                            <i class="fas fa-pencil-alt"></i>
                                            Edit
                                        </a>

                                            <form method="post"
                                                action="{{ route('datauser.destroy', ['user_id' => $user->user_id]) }}"
                                                style="display:inline;">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="deleteButton btn btn-danger btn-sm">
                                                    <i class="deletebutton fas fa-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="pagination-container">
                        {{ $users->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
