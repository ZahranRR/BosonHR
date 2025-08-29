@extends('layouts.app')
@section('title', 'Role/index')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role</h3>
                <div class="card-tools">
                    <a href="{{ route('role.create') }}" class="btn btn-primary" title="Create Role">
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
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 20%">Name</th>
                                <th style="width: 8%" class="text-center">Status</th>
                                <th style="width: 20%" class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td class="project-state text-center">
                                        <span
                                            class="badge {{ $role->status == 'enable' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($role->status) }}
                                        </span>
                                    </td>
                                    <td class="project-actions text-right">
                                        <a class="btn btn-info btn-sm" href="{{ route('role.edit', $role->id) }}">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                        @if ($role->name !== 'Superadmin')
                                            <form action="{{ route('role.destroy', $role->id) }}" method="POST"
                                                class="deleteForm" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="deleteButton btn btn-danger btn-sm">
                                                    <i class="deletebutton fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="pagination-container">
                        {{ $roles->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
