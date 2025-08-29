@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    {{-- <div>
        @if (Auth::user()->unreadNotifications->count() > 0)
            <div class="alert alert-info">
                <strong>Notifikasi:</strong>
                <ul>
                    @foreach (Auth::user()->unreadNotifications as $notification)
                        <li>
                            {{ $notification->data['status'] }}: Pengajuan cuti ID
                            #{{ $notification->data['offrequest_id'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="alert alert-success">
                Tidak ada notifikasi baru.
            </div>
        @endif
    </div> --}}

    <div class="row">
        <!-- Small Box for Total Employees -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner" style="color: white;">
                    <h3>{{ $totalEmployees }}</h3>
                    <p>Total Employees</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i> <!-- Ikon untuk karyawan -->
                </div>
            </div>
        </div>

        <!-- Small Box for Total Users -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner" style="color: white;">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people"></i> <!-- Ikon untuk pengguna -->
                </div>
            </div>
        </div>

        <!-- Small Box for Total Recruitments -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner" style="color: white;">
                    <h3>{{ $totalRecruitments }}</h3>
                    <p>Total Recruitments</p>
                </div>
                <div class="icon">
                    <i class="ion ion-document-text"></i> <!-- Ikon untuk recruitment -->
                </div>
            </div>
        </div>
    </div>

@endsection
