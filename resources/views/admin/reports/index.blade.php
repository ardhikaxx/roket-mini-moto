@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Laporan Penjualan</span>
@endsection

@section('content')
@php
    $user = auth()->user();
    $allCount = $reports->count();
    $pendingReports = $reports->where('status', 'diproses');
    $approvedReports = $reports->where('status', 'disetujui');
    $rejectedReports = $reports->where('status', 'ditolak');

    $pendingCount = $pendingReports->count();
    $approvedCount = $approvedReports->count();
    $rejectedCount = $rejectedReports->count();
    
    $totalRevenue = $approvedReports->sum('total_amount');
    $avgTransaction = $approvedCount > 0 ? $totalRevenue / $approvedCount : 0;
@endphp

<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-chart-line text-primary me-2"></i>Dashboard Laporan Penjualan</h1>
            <p class="page-subtitle">Analitik transaksi, persetujuan setoran, dan omzet bisnis</p>
        </div>
        <div class="page-actions d-flex gap-2">
            @if($user->isKaryawan())
            <a href="{{ route('karyawan.reports.create') }}" class="btn btn-primary px-4 py-2" style="font-weight:600; box-shadow:0 4px 12px rgba(230,57,70,0.25);"><i class="fa-solid fa-plus-circle me-2"></i> Buat Laporan Baru</a>
            @endif
        </div>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-4 mb-5 stagger-1">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); background:linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <p class="text-muted mb-0 fw-bold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Total Omzet (Disetujui)</p>
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fa-solid fa-sack-dollar"></i></div>
                </div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size:26px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0" style="font-size:12px;">Dari {{ $approvedCount }} transaksi valid</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100 cursor-pointer stat-filter-card" style="border-radius:var(--radius-lg);" onclick="filterTable('all')">
            <div class="card-body p-4 border-bottom border-4 border-primary rounded-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <p class="text-muted mb-0 fw-bold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Semua Laporan</p>
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--neutral-100);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fa-solid fa-file-invoice"></i></div>
                </div>
                <h3 class="fw-bold mb-1 text-dark" style="font-size:26px;">{{ $allCount }}</h3>
                <p class="text-muted mb-0" style="font-size:12px;">Klik untuk melihat semua</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100 cursor-pointer stat-filter-card" style="border-radius:var(--radius-lg);" onclick="filterTable('DIPROSES')">
            <div class="card-body p-4 border-bottom border-4 border-warning rounded-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <p class="text-muted mb-0 fw-bold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Menunggu Persetujuan</p>
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--warning-50);color:var(--warning-700);display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                </div>
                <h3 class="fw-bold mb-1" style="font-size:26px; color:var(--warning-700);">{{ $pendingCount }}</h3>
                <p class="text-muted mb-0" style="font-size:12px;">Perlu segera dicek</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100 cursor-pointer stat-filter-card" style="border-radius:var(--radius-lg);" onclick="filterTable('DITOLAK')">
            <div class="card-body p-4 border-bottom border-4 border-danger rounded-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <p class="text-muted mb-0 fw-bold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Laporan Ditolak</p>
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--danger-50);color:var(--danger);display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fa-solid fa-circle-xmark"></i></div>
                </div>
                <h3 class="fw-bold mb-1 text-danger" style="font-size:26px;">{{ $rejectedCount }}</h3>
                <p class="text-muted mb-0" style="font-size:12px;">Transaksi bermasalah</p>
            </div>
        </div>
    </div>
</div>

{{-- Card Filter & Ekspor Laporan --}}
<div class="card shadow-sm border-0 mb-4 stagger-1" style="border-radius:var(--radius-lg);">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center justify-content-between">
            <span><i class="fa-solid fa-filter text-primary me-2"></i> Filter & Ekspor Laporan</span>
            <div class="d-flex gap-2">
                <button type="button" onclick="exportData('excel')" class="btn btn-success btn-sm px-3 fw-bold text-white rounded-3">
                    <i class="fa-solid fa-file-excel me-1"></i> Cetak Excel
                </button>
                <button type="button" onclick="exportData('pdf')" class="btn btn-danger btn-sm px-3 fw-bold rounded-3">
                    <i class="fa-solid fa-file-pdf me-1"></i> Cetak PDF
                </button>
            </div>
        </h5>
    </div>
    <div class="card-body p-4 bg-neutral-50">
        <form method="GET" action="{{ route('admin.reports.index') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                {{-- Filter Toko --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold mb-1" style="font-size:12px;"><i class="fa-solid fa-store text-primary me-1"></i> Cabang Toko</label>
                    <select name="store_id" class="form-select" onchange="this.form.submit()">
                        <option value="all">-- Semua Toko --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Periode Waktu --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold mb-1" style="font-size:12px;"><i class="fa-regular fa-calendar-days text-primary me-1"></i> Periode Transaksi</label>
                    <select name="period" id="periodSelect" class="form-select" onchange="handlePeriodChange(this.value)">
                        <option value="all" {{ request('period', 'all') == 'all' ? 'selected' : '' }}>Semua Periode</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Rentang Tanggal Custom</option>
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold mb-1" style="font-size:12px;"><i class="fa-solid fa-tag text-primary me-1"></i> Status Laporan</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="col-12 col-md-6 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill fw-bold rounded-3"><i class="fa-solid fa-magnifying-glass me-1"></i> Terapkan</button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light border px-3 fw-bold rounded-3" title="Reset Filter"><i class="fa-solid fa-rotate-right"></i></a>
                </div>

                {{-- Custom Date Picker Row --}}
                <div class="col-12 mt-3" id="customDateRow" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                    <div class="p-3 rounded-3 border bg-white shadow-sm">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-5">
                                <label class="form-label mb-1 fw-semibold" style="font-size:12px;">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label mb-1 fw-semibold" style="font-size:12px;">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-12 col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-dark w-100 fw-bold mt-4" style="border-radius:8px;">Filter Tanggal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function handlePeriodChange(val) {
        const row = document.getElementById('customDateRow');
        if (val === 'custom') {
            if (row) row.style.display = 'block';
        } else {
            if (row) row.style.display = 'none';
            document.getElementById('filterForm').submit();
        }
    }

    function exportData(type) {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        
        if (type === 'excel') {
            window.location.href = "{{ route('admin.reports.export-excel') }}?" + params;
        } else if (type === 'pdf') {
            window.open("{{ route('admin.reports.export-pdf') }}?" + params, '_blank');
        }
    }
</script>

{{-- Area Data Laporan --}}
<div class="card shadow-sm border-0 stagger-1 mb-5" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-light gap-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-list-ul text-primary me-2"></i> Rincian Transaksi</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-light" style="border:1px solid var(--border-light); font-weight:600;" onclick="filterTable('all')"><i class="fa-solid fa-rotate-right me-1"></i> Reset Filter</button>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover align-middle" id="reportsTable" style="margin:0; min-width:1000px;">
                <thead>
                    <tr>
                        <th style="width:80px;">Bukti</th>
                        <th style="min-width:200px;">Informasi Transaksi</th>
                        <th style="width:200px;">Produk Terjual</th>
                        <th style="width:150px;">Total Setoran</th>
                        <th style="width:140px;">Status</th>
                        <th class="text-end" style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $r)
                    <tr>
                        <td>
                            @if($r->images->first())
                            <div style="width:48px;height:48px;border-radius:10px;overflow:hidden;cursor:pointer;border:1px solid var(--border-light);box-shadow:var(--shadow-sm);" onclick="openLightbox('{{ asset('storage/'.$r->images->first()->image_path) }}')">
                                <img src="{{ asset('storage/'.$r->images->first()->image_path) }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('assets/images/no-image.png') }}';">
                            </div>
                            @else
                            <div style="width:48px;height:48px;border-radius:10px;overflow:hidden;border:1px solid var(--border-light);box-shadow:var(--shadow-sm);">
                                <img src="{{ asset('assets/images/no-image.png') }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1" style="font-size:15px;">{{ $r->user->name ?? 'User Tidak Diketahui' }}</div>
                            <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                <span class="badge bg-light text-dark border"><i class="fa-solid fa-store text-muted me-1"></i> {{ $r->store->name ?? '-' }}</span>
                                <span class="text-muted" style="font-size:12px;"><i class="fa-regular fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($r->transaction_date)->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @php $itemNames = $r->items->pluck('product_name')->take(2); @endphp
                                @foreach($itemNames as $in)
                                    <span class="badge badge-neutral" style="font-weight:normal; font-size:11px;">{{ Str::limit($in, 15) }}</span>
                                @endforeach
                                @if($r->items->count() > 2)
                                    <span class="badge bg-light text-primary border" style="font-weight:bold; font-size:11px;">+{{ $r->items->count() - 2 }} Item</span>
                                @endif
                                @if($r->items->count() == 0)
                                    <span class="text-muted" style="font-size:12px;font-style:italic;">Tidak ada data item</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary" style="font-size:15px;">Rp {{ number_format($r->total_amount,0,',','.') }}</div>
                        </td>
                        <td>
                            @if($r->status == 'diproses') 
                                <span class="badge badge-warning rounded-pill px-3 py-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-clock-rotate-left me-1"></i> DIPROSES</span>
                            @elseif($r->status == 'disetujui') 
                                <span class="badge badge-success rounded-pill px-3 py-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-check-circle me-1"></i> DISETUJUI</span>
                            @else 
                                <span class="badge badge-danger rounded-pill px-3 py-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-3" style="border:1px solid var(--border-light);background:white;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical text-secondary"></i></button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="min-width: 180px;">
                                    <a href="{{ route('admin.reports.show', $r->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-eye me-2 w-15px text-center"></i> Detail Laporan</a>
                                    
                                    @if($r->status == 'diproses' && ($user->isAdmin() || $user->isKepalaToko()))
                                    <div class="dropdown-divider my-1"></div>
                                    <form action="{{ route('admin.reports.approve', $r->id) }}" method="POST" id="approve-{{ $r->id }}" style="display:inline;">
                                        @csrf
                                        <button type="button" class="dropdown-item py-2 fw-semibold text-success" style="font-size:14px;" onclick="confirmApprove({{ $r->id }})"><i class="fa-solid fa-check me-2 w-15px text-center"></i> Setujui Laporan</button>
                                    </form>
                                    <button type="button" class="dropdown-item py-2 fw-semibold text-warning" style="font-size:14px;" onclick="showReject({{ $r->id }})"><i class="fa-solid fa-xmark me-2 w-15px text-center"></i> Tolak Laporan</button>
                                    @endif
                                    
                                    @if($user->isAdmin())
                                    <div class="dropdown-divider my-1"></div>
                                    <form action="{{ route('admin.reports.destroy', $r->id) }}" method="POST" id="del-report-{{ $r->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="dropdown-item py-2 fw-semibold text-danger" style="font-size:14px;" onclick="confirmDeleteReport({{ $r->id }})"><i class="fa-regular fa-trash-can me-2 w-15px text-center"></i> Hapus Permanen</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Logic pencarian DataTables dari Card KPI
function filterTable(status) {
    const table = $('#reportsTable').DataTable();
    if (status === 'all') { 
        table.search('').columns().search('').draw(); 
    } else { 
        table.column(4).search(status).draw(); // Kolom ke-4 (indeks 4) adalah status
    }
}

// Menutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

function confirmApprove(id) {
    Swal.fire({
        title: 'Setujui Laporan?',
        html: `Laporan yang disetujui akan diakumulasikan ke omzet toko dan stok produk akan <strong>dikurangi secara permanen</strong>.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2b9348',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Ya, Setujui',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('approve-'+id).submit(); 
    });
}

function showReject(id) {
    Swal.fire({
        title: 'Tolak Laporan',
        html: '<div class="form-group mb-0"><label class="form-label text-start d-block fw-semibold mb-2">Alasan Penolakan <span class="text-danger">*</span></label><textarea id="rejectReason" class="form-control" rows="3" placeholder="Tulis alasan mengapa laporan ini tidak valid..." required></textarea></div>',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-xmark me-1"></i> Tolak Laporan',
        cancelButtonText: 'Batal',
        preConfirm: () => { 
            const r = document.getElementById('rejectReason').value; 
            if(!r) { Swal.showValidationMessage('Alasan penolakan wajib diisi!'); } 
            return r; 
        },
        customClass: { popup: 'rounded-4' }
    }).then((res) => {
        if(res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST'; 
            form.action = '{{ url("admin/reports") }}/'+id+'/reject';
            form.innerHTML = '@csrf<input type="hidden" name="rejection_reason" value="'+res.value+'">';
            document.body.appendChild(form); form.submit();
        }
    });
}

function confirmDeleteReport(id) {
    Swal.fire({
        title: 'Hapus Laporan?',
        html: `Data laporan ini akan <strong>dihapus secara permanen</strong> dari sistem.<br>Aksi ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('del-report-'+id).submit(); 
    });
}
</script>
@endsection
