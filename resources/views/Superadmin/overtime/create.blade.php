@extends('layouts.app')
@section('title', 'Overtime/create')
@section('content')
   
    <div class="content">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-xl-6">
                        <h1>Overtime</h1>
                    </div>
                </div>
            </div>
        </section>
    
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Create Overtime</h3>
                    </div>
    
                    <form action="{{ route('overtime.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
    
                            <!-- Employee Information (Readonly) -->
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}" readonly>
                            </div>
    
                            <!-- Overtime Date -->
                            <div class="form-group">
                                <label for="overtime_date">Overtime Date</label>
                                <input type="date" name="overtime_date" id="overtime_date" class="form-control" required>
                            </div>
    
                            <!-- Overtime Duration -->
                            <div class="form-group">
                                <label for="duration" class="form-label">Duration (in hours)</label>
                                <input type="number" name="duration" id="duration" class="form-control" min="1" max="8" step="1" required placeholder="Enter duration in whole hours">
                                <div class="invalid-feedback" id="duration-feedback" style="display: none;">
                                    Duration must be a whole number between 1 and 8 hours.
                                </div>
                            </div>
    
                            <!-- Overtime Notes -->
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" required></textarea>
                            </div>
    
                            <!-- Select Manager -->
                            <div class="form-group">
                                <label for="manager_id">Select Manager</label>
                                <select name="manager_id" id="manager_id" class="form-control" required>
                                    <option value="">Select Manager</option>
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->user_id }}">{{ $manager->name }}</option>
                                    @endforeach
                                </select>
                            </div>
    
                        </div>
    
                        <!-- Save Button -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
      


@endsection
