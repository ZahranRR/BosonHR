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

                <form action="{{ route('overtime.store') }}" method="POST" enctype="multipart/form-data">
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
                            <select name="duration" id="duration" class="form-control" required>
                                <option value="">-- Select Duration --</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>


                        <!-- Overtime Notes -->
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" required></textarea>
                        </div>

                        <!-- Attachment File (Image) -->
                        <div class="form-group">
                            <label for="attachment">Attachment (Image)</label>
                            <input type="file" name="attachment" id="attachment" class="form-control" accept="image/*">
                            <small class="text-muted">Upload bukti foto (format: jpg, png, jpeg, max 2MB)</small>
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