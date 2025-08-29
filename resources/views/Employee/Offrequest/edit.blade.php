@extends('layouts.app')

@section('title', 'Edit Offrequest')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Edit Off Request</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('offrequest.update', $offrequest->offrequest_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Nama (Readonly) -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $offrequest->user->name }}" readonly>
                    </div>

                    <!-- Email (Readonly) -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $offrequest->user->email }}" readonly>
                    </div>

                    <!-- Manager (Readonly) -->
                    <div class="form-group">
                        <label for="manager_id">Manager</label>
                        <input type="text" name="manager" class="form-control" value="{{ $offrequest->manager->name ?? 'Tidak Ada Manager' }}" readonly>
                    </div>

                    <!-- Title (Readonly) -->
                    <div class="form-group">
                        <label for="title">Off Type</label>
                        <input type="text" name="title" class="form-control" value="{{ $offrequest->title }}" readonly>
                    </div>

                    <!-- Deskripsi (Readonly) -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" readonly>{{ $offrequest->description }}</textarea>
                    </div>

                    <!-- Start Event (Readonly) -->
                    <div class="form-group">
                        <label for="start_event">Start Event</label>
                        <input type="date" name="start_event" class="form-control" value="{{ $offrequest->start_event->format('Y-m-d') }}" readonly>
                    </div>

                    <!-- End Event (Readonly) -->
                    <div class="form-group">
                        <label for="end_event">End Event</label>
                        <input type="date" name="end_event" class="form-control" value="{{ $offrequest->end_event->format('Y-m-d') }}" readonly>
                    </div>

                    <!-- Gambar -->
                    <div class="form-group">
                        <label for="image">Image</label>
                        @if ($offrequest->image)
                            <div>
                                <img src="{{ asset('uploads/' . $offrequest->image) }}" alt="Gambar Saat Ini" class="img-thumbnail" width="150">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Update Image</button>
                </form>
            </div>
        </div>
    </section>
@endsection
