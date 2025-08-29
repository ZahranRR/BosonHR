@extends('layouts.app')
@section('title', 'Resignation Requests/Create')
@section('content')
    <div class="container-fluid">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Submit Your Resignation</h3>
                    </div>

                    <form method="POST" action="{{ route('resignationrequest.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <!-- Name Input -->
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="{{ Auth::user()->name }}" readonly>
                            </div>

                            <!-- Resign Date -->
                            <div class="form-group">
                                <label for="resign_date">Resign Date</label>
                                <input type="date" class="form-control" name="resign_date" id="resign_date"
                                    min="{{ now()->toDateString() }}" required>
                            </div>

                            <!-- Reason for Resignation -->
                            <div class="form-group">
                                <label for="reason">Reason</label>
                                <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
                            </div>

                            <!-- Remarks (optional) -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                            </div>

                            <!-- Manager Selection Dropdown -->
                            <div class="form-group">
                                <label for="manager_id">Select Manager</label>
                                @if ($managers->isNotEmpty())
                                    <select class="form-control" name="manager_id" id="manager_id" required>
                                        @foreach ($managers as $manager)
                                            <option value="{{ $manager->user_id }}">{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="text-danger">No managers available. Please contact the admin.</p>
                                @endif
                            </div>

                            <!-- Document Upload -->
                            <div class="form-group">
                                <label for="document">Upload Supporting Document</label>
                                <input type="file" class="form-control" name="document" id="document"
                                    accept=".pdf,.doc,.docx">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('resignationrequest.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
