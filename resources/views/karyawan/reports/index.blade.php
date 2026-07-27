@extends('layouts.admin')
@section('title', 'Laporan Penjualan Saya')
@section('breadcrumb')
    <a href="{{ route('karyawan.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Laporan Saya</span>
@endsection
@section('content')
@php
    $user = auth()->user();
@endphp
<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-file-invoice text-primary me-2"></i>Laporan Penjualan Saya</h1>
            <p class="page-subtitle">Total omzet disetujui: <strong class="text-success">Rp {{ number_format($totalApproved,0,',','.') }}</strong></p>
        </div>
        <div class="page-actions">
            <a href="{{ route('karyawan.reports.create') }}" class="btn btn-primary px-4 py-2" style="font-weight:600;"><i class="fa-solid fa-plus me-2"></i> Buat Laporan Baru</a>
        </div>
    </div>
</div>

{{-- Card Filter & Ekspor Karyawan --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
    <div class="card-header bg-white p-4 border-bottom border-light d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
            <i class="fa-solid fa-filter text-primary me-2"></i> Filter & Ekspor Laporan Saya
        </h5>
        <div class="d-flex align-items-center gap-2 ms-auto">
            <button type="button" onclick="exportKaryawanData('excel')" class="btn btn-success px-3 py-2 fw-bold text-white rounded-3 d-inline-flex align-items-center gap-2" style="font-size:13px; box-shadow:0 4px 10px rgba(40,167,69,0.2);">
                <i class="fa-solid fa-file-excel"></i>
                <span>Cetak Excel</span>
            </button>
            <button type="button" onclick="exportKaryawanData('pdf')" class="btn btn-danger px-3 py-2 fw-bold rounded-3 d-inline-flex align-items-center gap-2" style="font-size:13px; box-shadow:0 4px 10px rgba(230,57,70,0.2);">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Cetak PDF</span>
            </button>
        </div>
    </div>
    <div class="card-body p-4 bg-neutral-50">
        <form method="GET" action="{{ route('karyawan.reports.index') }}" id="karyawanFilterForm">
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
                    <label class="form-label fw-bold mb-1" style="font-size:12px;"><i class="fa-regular fa-calendar-days text-primary me-1"></i> Periode Waktu</label>
                    <select name="period" id="karyawanPeriodSelect" class="form-select" onchange="handleKaryawanPeriodChange(this.value)">
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
                    <a href="{{ route('karyawan.reports.index') }}" class="btn btn-light border px-3 fw-bold rounded-3" title="Reset Filter"><i class="fa-solid fa-rotate-right"></i></a>
                </div>

                {{-- Custom Date Row --}}
                <div class="col-12 mt-3" id="karyawanCustomDateRow" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
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
    function handleKaryawanPeriodChange(val) {
        const row = document.getElementById('karyawanCustomDateRow');
        if (val === 'custom') {
            if (row) row.style.display = 'block';
        } else {
            if (row) row.style.display = 'none';
            document.getElementById('karyawanFilterForm').submit();
        }
    }

    function exportKaryawanData(type) {
        const form = document.getElementById('karyawanFilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        
        if (type === 'excel') {
            window.location.href = "{{ route('karyawan.reports.export-excel') }}?" + params;
        } else if (type === 'pdf') {
            window.open("{{ route('karyawan.reports.export-pdf') }}?" + params, '_blank');
        }
    }
</script>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover align-middle" style="margin:0; min-width:700px;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Toko</th>
                        <th style="width:100px;">Total Item</th>
                        <th style="width:180px;">Total Penjualan</th>
                        <th style="width:140px;">Status</th>
                        <th class="text-end" style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $r)
                    <tr>
                        <td><span class="text-nowrap">{{ $r->created_at->format('d/m/Y H:i') }}</span></td>
                        <td>{{ $r->store->name ?? '-' }}</td>
                        <td><span class="fw-semibold">{{ $r->total_items }}</span></td>
                        <td class="fw-bold text-primary">Rp {{ number_format($r->total_amount,0,',','.') }}</td>
                        <td>
                            @if($r->status == 'diproses') <span class="badge badge-warning rounded-pill"><i class="fa-solid fa-clock-rotate-left me-1"></i> DIPROSES</span>
                            @elseif($r->status == 'disetujui') <span class="badge badge-success rounded-pill"><i class="fa-solid fa-check-circle me-1"></i> DISETUJUI</span>
                            @else <span class="badge badge-danger rounded-pill"><i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-3" style="border:1px solid var(--border-light);background:white;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical text-secondary"></i></button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="min-width: 160px;">
                                    <a href="{{ route('admin.reports.show', $r->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-eye me-2 w-15px text-center"></i> Detail</a>
                                    @if($r->status == 'ditolak')
                                    <a href="{{ route('karyawan.reports.edit', $r->id) }}" class="dropdown-item py-2 fw-semibold text-warning" style="font-size:14px;"><i class="fa-solid fa-pen me-2 w-15px text-center"></i> Perbaiki</a>
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
@endsection
