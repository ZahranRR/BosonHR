@php
    // Fungsi untuk memotong teks (cutText)
    function cutText($text, $length)
    {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    // Ambil tab aktif dari query string, default ke 'violation'
    $activeTab = request()->query('tab', 'violation');
@endphp

@extends('layouts.app')
@section('title', 'Employeebooks/index')
@section('content')
    <div class="container-fluid">
        <div class="card mb-3 w-100">
            <div class="card-header">
                <h1>Employee Books</h1>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="employeeBookTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'violation' ? 'active' : '' }}" id="violation-tab"
                            data-toggle="tab" href="#violation" role="tab" aria-controls="violation"
                            aria-selected="{{ $activeTab === 'violation' ? 'true' : 'false' }}">
                            Violation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'warning' ? 'active' : '' }}" id="warning-tab"
                            data-toggle="tab" href="#warning" role="tab" aria-controls="warning"
                            aria-selected="{{ $activeTab === 'warning' ? 'true' : 'false' }}">
                            Warning
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'reprimand' ? 'active' : '' }}" id="reprimand-tab"
                            data-toggle="tab" href="#reprimand" role="tab" aria-controls="reprimand"
                            aria-selected="{{ $activeTab === 'reprimand' ? 'true' : 'false' }}">
                            Reprimand
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="employeeBookTabContent">
                    <!-- Violation Tab -->
                    <div id="violation" class="tab-pane fade {{ $activeTab === 'violation' ? 'show active' : '' }}">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3>Violation</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('employeebooks.index') }}" method="GET" class="form-inline mb-3">
                                    <!-- Search by employee name -->
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by employee name" value="{{ request()->query('search') }}">

                                    <!-- Search by type_of -->
                                    <select name="type_of" class="form-control ml-2">
                                        <option value="">Select Type</option>
                                        <option value="SOP" {{ request()->query('type_of') == 'SOP' ? 'selected' : '' }}>
                                            SOP</option>
                                        <option value="Administrative"
                                            {{ request()->query('type_of') == 'Administrative' ? 'selected' : '' }}>
                                            Administrative</option>
                                        <option value="Behavior"
                                            {{ request()->query('type_of') == 'Behavior' ? 'selected' : '' }}>Behavior
                                        </option>
                                    </select>

                                    <!-- Hidden Tab Input -->
                                    <input type="hidden" name="tab" value="reprimand">

                                    <button type="submit" class="btn btn-secondary ml-2">Search</button>
                                </form>

                                <a href="{{ route('employeebooks.create') }}?category=violation"
                                    class="btn btn-primary mb-3">
                                    <i class="fas fa-plus"></i> Add Violation
                                </a>
                                <div class="table-responsive">
                                    <table class="table table-striped projects">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Incident Date</th>
                                                <th>Detail</th>
                                                <th>Remarks</th>
                                                <th>Type of</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($violations as $violation)
                                                <tr>
                                                    <td>{{ $violation->employee->first_name }}
                                                        {{ $violation->employee->last_name }}</td>
                                                    <td>{{ $violation->incident_date }}</td>
                                                    <td>{{ cutText($violation->incident_detail, 50) }}</td>
                                                    <td>{{ cutText($violation->remarks, 50) }}</td>
                                                    <td>{{ $violation->type_of }}</td>
                                                    <td>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('employeebooks.edit', $violation->employeebook_id) }}">
                                                            <i class="fas fa-pencil-alt"></i> Edit
                                                        </a>
                                                        <form
                                                            action="{{ route('employeebooks.destroy', $violation->employeebook_id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="deletebutton btn btn-danger btn-sm">
                                                                <i class="deletebutton fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('employeebooks.detail', $violation->employeebook_id) }}">
                                                            <i class="fas fa-info-circle"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{ $violations->links('vendor.pagination.adminlte') }}
                    </div>

                    <!-- Warning Tab -->
                    <div id="warning" class="tab-pane fade {{ $activeTab === 'warning' ? 'show active' : '' }}">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3>Warning</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('employeebooks.index') }}" method="GET" class="form-inline mb-3">
                                    <!-- Search by employee name -->
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by employee name" value="{{ request()->query('search') }}">

                                    <!-- Search by type_of -->
                                    <select name="type_of" class="form-control ml-2">
                                        <option value="">Select Type</option>
                                        <option value="SOP"
                                            {{ request()->query('type_of') == 'SOP' ? 'selected' : '' }}>SOP</option>
                                        <option value="Administrative"
                                            {{ request()->query('type_of') == 'Administrative' ? 'selected' : '' }}>
                                            Administrative</option>
                                        <option value="Behavior"
                                            {{ request()->query('type_of') == 'Behavior' ? 'selected' : '' }}>Behavior
                                        </option>
                                    </select>

                                    <!-- Hidden Tab Input -->
                                    <input type="hidden" name="tab" value="reprimand">

                                    <button type="submit" class="btn btn-secondary ml-2">Search</button>
                                </form>

                                <a href="{{ route('employeebooks.create') }}?category=warning"
                                    class="btn btn-primary mb-3">
                                    <i class="fas fa-plus"></i> Add Warning
                                </a>
                                <div class="table-responsive">
                                    <table class="table table-striped projects">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Incident Date</th>
                                                <th>Detail</th>
                                                <th>Remarks</th>
                                                <th>Type of</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($warnings as $warning)
                                                <tr>
                                                    <td>{{ $warning->employee->first_name }}
                                                        {{ $warning->employee->last_name }}</td>
                                                    <td>{{ $warning->incident_date }}</td>
                                                    <td>{{ cutText($warning->incident_detail, 50) }}</td>
                                                    <td>{{ cutText($warning->remarks, 50) }}</td>
                                                    <td>{{ $warning->type_of }}</td>
                                                    <td>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('employeebooks.edit', $warning->employeebook_id) }}">
                                                            <i class="fas fa-pencil-alt"></i> Edit
                                                        </a>
                                                        <form
                                                            action="{{ route('employeebooks.destroy', $warning->employeebook_id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="deletebutton btn btn-danger btn-sm">
                                                                <i class="deletebutton fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('employeebooks.detail', $warning->employeebook_id) }}">
                                                            <i class="fas fa-info-circle"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{ $warnings->links('vendor.pagination.adminlte') }}
                    </div>

                    <!-- Reprimand Tab -->
                    <div id="reprimand" class="tab-pane fade {{ $activeTab === 'reprimand' ? 'show active' : '' }}">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3>Reprimand</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('employeebooks.index') }}" method="GET"
                                    class="form-inline mb-3">
                                    <!-- Search by employee name -->
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by employee name" value="{{ request()->query('search') }}">

                                    <!-- Search by type_of -->
                                    <select name="type_of" class="form-control ml-2">
                                        <option value="">Select Type</option>
                                        <option value="SOP"
                                            {{ request()->query('type_of') == 'SOP' ? 'selected' : '' }}>SOP</option>
                                        <option value="Administrative"
                                            {{ request()->query('type_of') == 'Administrative' ? 'selected' : '' }}>
                                            Administrative</option>
                                        <option value="Behavior"
                                            {{ request()->query('type_of') == 'Behavior' ? 'selected' : '' }}>Behavior
                                        </option>
                                    </select>

                                    <!-- Hidden Tab Input -->
                                    <input type="hidden" name="tab" value="reprimand">

                                    <button type="submit" class="btn btn-secondary ml-2">Search</button>
                                </form>

                                <a href="{{ route('employeebooks.create') }}?category=reprimand"
                                    class="btn btn-primary mb-3">
                                    <i class="fas fa-plus"></i> Add Reprimand
                                </a>
                                <div class="table-responsive">
                                    <table class="table table-striped projects">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Incident Date</th>
                                                <th>Detail</th>
                                                <th>Remarks</th>
                                                <th>Type of</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reprimands as $reprimand)
                                                <tr>
                                                    <td>{{ $reprimand->employee->first_name }}
                                                        {{ $reprimand->employee->last_name }}</td>
                                                    <td>{{ $reprimand->incident_date }}</td>
                                                    <td>{{ cutText($reprimand->incident_detail, 50) }}</td>
                                                    <td>{{ cutText($reprimand->remarks, 50) }}</td>
                                                    <td>{{ $reprimand->type_of }}</td>
                                                    <td>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('employeebooks.edit', $reprimand->employeebook_id) }}">
                                                            <i class="fas fa-pencil-alt"></i> Edit
                                                        </a>
                                                        <form
                                                            action="{{ route('employeebooks.destroy', $reprimand->employeebook_id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="deletebutton btn btn-danger btn-sm">
                                                                <i class="deletebutton fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('employeebooks.detail', $reprimand->employeebook_id) }}">
                                                            <i class="fas fa-info-circle"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{ $reprimands->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
