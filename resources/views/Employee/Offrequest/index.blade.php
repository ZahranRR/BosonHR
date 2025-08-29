@extends('layouts.app')
@section('title', 'Offrequest/index')
@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Leave Request List</h3>
                <div class="card-tools">
                    <a class="btn btn-primary btn-sm" href="{{ route('offrequest.create') }}">
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

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Total Leave by Type</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Total Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($totals as $total)
                                    <tr>
                                        <td>{{ $total->title }}</td>
                                        <td>{{ $total->total_days }} day</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Manager</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Start Event</th>
                                <th>End Event</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Picture Proof</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($offrequests as $offrequest)
                                <tr>
                                    <td>{{ $offrequest->user->name ?? 'N/A' }}</td>
                                    <td>{{ $offrequest->user->email ?? 'N/A' }}</td>
                                    <td>{{ $offrequest->manager ? $offrequest->manager->name : 'N/A' }}</td>
                                    <td>{{ $offrequest->title }}</td>
                                    <td>{{ $offrequest->description }}</td>
                                    <td>{{ $offrequest->start_event->format('Y-m-d') }}</td>
                                    <td>{{ $offrequest->end_event->format('Y-m-d') }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $offrequest->status == 'approved' ? 'bg-success' : ($offrequest->status == 'rejected' ? 'bg-danger' : 'bg-secondary') }}">
                                            {{ ucfirst($offrequest->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $offrequest->approved_by ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($offrequest->image)
                                            <img src="{{ asset('uploads/' . $offrequest->image) }}" alt="Bukti Cuti"
                                                width="100" style="cursor: pointer;"
                                                onclick="showImage('{{ asset('uploads/' . $offrequest->image) }}')">
                                        @else
                                            <span class="text-muted" style="cursor: pointer;"
                                                onclick="openUploadModal({{ $offrequest->id }})">
                                                No proof provided
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('offrequest.edit', $offrequest->offrequest_id) }}">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">There are no leave requests available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <div class="pagination-container">
                        {{ $offrequests->links('vendor.pagination.adminlte') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk menampilkan gambar -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Picture Proof</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Bukti Cuti">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk upload gambar -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel"
        aria-hidden="true">
        <form id="uploadImageForm" method="POST" enctype="multipart/form-data" action="">
            @csrf
            @method('POST')
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadImageModalLabel">Upload Proof of Leave</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="offrequest_id" name="offrequest_id">
                        <div class="form-group">
                            <label for="image">Upload Image</label>
                            <input type="file" name="image" class="form-control" id="image" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function showImage(src) {
            document.getElementById('modalImage').src = src;
            $('#imagePreviewModal').modal('show');
        }

        function openUploadModal(offrequestId) {
            document.getElementById('uploadImageForm').action = "{{ url('offrequest/upload') }}/" + offrequestId;
            document.getElementById('offrequest_id').value = offrequestId;
            $('#uploadImageModal').modal('show');
        }
    </script>
@endsection
