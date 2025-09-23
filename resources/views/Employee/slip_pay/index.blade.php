@extends('layouts.app')
@section('title', 'Slip Gaji')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h3 class="card-title mb-0">Slip Gaji</h3>
                    <div class="d-flex align-items-center">
                        {{-- Filter bulan payroll --}}
                        <form method="GET" action="{{ route('slippay.index') }}"
                            class="form-inline d-flex mb-0 align-items-center">
                            <input type="month" name="month" class="form-control"
                                value="{{ request()->query('month', $selectedMonth ?? now()->format('Y-m')) }}">
                            <button type="submit" class="btn btn-secondary ml-2">Search</button>
                        </form>

                        {{-- Tombol download --}}
                        @if($payroll)
                            <a href="{{ route('slippay.download', $payroll->payroll_id) }}" class="btn btn-primary ml-2">
                                <i class="fas fa-download"></i> Download
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if($payroll)
                        <iframe src="{{ route('slippay.preview', $payroll->payroll_id) }}" width="100%" height="700"
                            style="border:1px solid #ddd; border-radius:8px;"></iframe>
                    @else
                        <p class="text-center">Belum ada slip gaji untuk Anda.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection