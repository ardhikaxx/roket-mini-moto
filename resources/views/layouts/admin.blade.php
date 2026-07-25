<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Roket Mini Moto')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; color: #fff; }
        .sidebar a { color: #c2c7d0; text-decoration: none; display: block; padding: 10px 15px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        .content { width: 100%; padding: 20px; }
    </style>
    @stack('styles')
</head>
<body class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar p-3" style="width: 250px;">
        <h4 class="text-center text-white mb-4">Roket Mini Moto</h4>
        
        <div class="mb-3 text-center">
            <div class="badge bg-primary fs-6">{{ auth()->user()->name }}</div>
            <div class="small text-muted mt-1">{{ strtoupper(auth()->user()->role) }}</div>
        </div>

        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isKepalaToko() ? route('kepalatoko.dashboard') : route('karyawan.dashboard')) }}" class="{{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge me-2"></i> Dashboard
        </a>

        @if(auth()->user()->isAdmin())
        <div class="sidebar-heading mt-4 mb-2 text-uppercase text-muted small fw-bold px-3">Master Data</div>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags me-2"></i> Kategori Produk
        </a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box me-2"></i> Manajemen Produk
        </a>
        <a href="{{ route('admin.stores.index') }}" class="{{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
            <i class="fa-solid fa-store me-2"></i> Manajemen Toko
        </a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users me-2"></i> Manajemen Pengguna
        </a>
        @endif

        <div class="sidebar-heading mt-4 mb-2 text-uppercase text-muted small fw-bold px-3">Transaksi</div>
        
        @if(auth()->user()->isAdmin() || auth()->user()->isKepalaToko())
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i> Laporan Penjualan
        </a>
        @endif

        @if(!auth()->user()->isAdmin() && !auth()->user()->isKepalaToko())
        <a href="{{ route('karyawan.reports.index') }}" class="{{ request()->routeIs('karyawan.reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i> Laporan Saya
        </a>
        <a href="{{ route('karyawan.reports.create') }}" class="{{ request()->routeIs('karyawan.reports.create') ? 'active' : '' }}">
            <i class="fa-solid fa-plus-circle me-2"></i> Buat Laporan Baru
        </a>
        @endif

        <hr class="border-secondary">
        <a href="#" onclick="event.preventDefault(); confirmLogout();">
            <i class="fa-solid fa-right-from-bracket me-2 text-danger"></i> <span class="text-danger">Logout</span>
        </a>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}'
                });
            </script>
        @endif

        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }
        $(document).ready(function() {
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
