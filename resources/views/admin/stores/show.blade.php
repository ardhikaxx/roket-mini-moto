@extends('layouts.admin')
@section('title', $store->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stores.index') }}">Toko</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $store->name }}</span>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            @if($store->photo)
            <div style="height:180px;overflow:hidden;border-radius:var(--radius-lg) var(--radius-lg) 0 0;"><img src="{{ asset('storage/'.$store->photo) }}" style="width:100%;height:100%;object-fit:cover;"></div>
            @endif
            <div class="card-body">
                <span class="badge badge-neutral mb-2">{{ $store->code }}</span>
                <h4 class="fw-bold mb-1">{{ $store->name }}</h4>
                <div style="font-size:13px;color:var(--text-secondary);margin-bottom:8px;">
                    <i class="fa-solid fa-location-dot me-1"></i> {{ $store->address }}<br>
                    @if($store->phone)<i class="fa-solid fa-phone me-1"></i> {{ $store->phone }}<br>@endif
                    @if($store->operational_hours)<i class="fa-solid fa-clock me-1"></i> {{ $store->operational_hours }}@endif
                </div>
                <span class="badge {{ $store->is_active ? 'badge-success' : 'badge-danger' }}">{{ $store->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('admin.stores.edit', $store->id) }}" class="btn btn-primary btn-sm w-100"><i class="fa-regular fa-pen-to-square"></i> Edit Toko</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row mb-4">
            <div class="col-4"><div class="stat-card" style="padding:14px;cursor:default;"><div class="stat-label">Total Omzet</div><div class="stat-value" style="font-size:20px;">Rp {{ number_format($totalOmzet,0,',','.') }}</div></div></div>
            <div class="col-4"><div class="stat-card" style="padding:14px;cursor:default;"><div class="stat-label">Laporan Disetujui</div><div class="stat-value" style="font-size:20px;">{{ $totalReports }}</div></div></div>
            <div class="col-4"><div class="stat-card" style="padding:14px;cursor:default;"><div class="stat-label">Karyawan</div><div class="stat-value" style="font-size:20px;">{{ $store->users->count() }}</div></div></div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="fw-bold mb-0">Karyawan</h5></div>
            <div class="card-body" style="padding:16px 20px;">
                @forelse($store->users as $u)
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="avatar avatar-sm">{{ strtoupper(substr($u->name,0,2)) }}</div>
                    <div style="flex:1;"><div class="fw-semibold" style="font-size:14px;">{{ $u->name }}</div><div style="font-size:12px;color:var(--text-secondary);">{{ str_replace('_',' ',ucfirst($u->role)) }} &middot; {{ $u->sales_reports_count ?? 0 }} laporan</div></div>
                    <span class="badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                @empty
                <div class="text-muted text-sm">Belum ada karyawan</div>
                @endforelse
            </div>
        </div>

        @if($topProducts->count() > 0)
        <div class="card">
            <div class="card-header"><h5 class="fw-bold mb-0">Produk Terlaris</h5></div>
            <div class="card-body" style="padding:16px 20px;">
                @foreach($topProducts as $tp)
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="avatar avatar-sm" style="background:var(--primary-50);color:var(--primary);">{{ $loop->iteration }}</div>
                    <div><div class="fw-semibold" style="font-size:14px;">{{ $tp->product->name ?? '-' }}</div><div style="font-size:12px;color:var(--text-secondary);">{{ $tp->total_qty }} terjual</div></div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
