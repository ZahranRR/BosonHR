<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="{{ asset('dist/img/icongrob.png') }}" type="image/x-icon" />
    <title>@yield('title', 'Grobmedia')</title>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Tambahkan di dalam <head> jika belum ada -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @include('components.head')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.navbar')
        <!-- Sidebar -->
        @include('partials.sidebar')
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Footer -->
        @include('layouts.footer')
    </div>
    @include('components.script')

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <!-- Include JS Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        let idleTime = 0;
        const timeout = 60 * 60 * 1000; // 1 jam dalam milidetik

        // Reset idle timer saat pengguna melakukan aktivitas
        function resetIdleTimer() {
            idleTime = 0;
        }

        // Tambahkan event listener untuk mendeteksi aktivitas
        document.onmousemove = resetIdleTimer;
        document.onkeypress = resetIdleTimer;

        // Timer untuk mengecek idle time
        setInterval(() => {
            idleTime += 1000;
            if (idleTime >= timeout) {
                window.location.href = '{{ route('login') }}';
            }
        }, 1000);
    </script>



</body>

</html>
