@extends('layouts.admin')
@section('title', 'Manajemen Toko')

@section('content')
@php
    $totalStores = \App\Models\Store::count();
    $activeStores = \App\Models\Store::where('is_active', true)->count();
    $inactiveStores = $totalStores - $activeStores;
@endphp

<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-store text-primary me-2"></i>Jaringan Cabang Toko</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;">Kelola informasi dan status operasional seluruh cabang Anda</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.stores.create') }}" class="btn btn-primary px-4 py-2" style="font-weight:600; box-shadow:0 4px 12px rgba(230,57,70,0.25);">
                <i class="fa-solid fa-plus-circle me-2"></i> Tambah Toko Baru
            </a>
        </div>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-4 mb-4 stagger-1">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); background:linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:20px;">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Total Cabang</p>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size:24px;">{{ $totalStores }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:var(--success-50);color:var(--success);display:flex;align-items:center;justify-content:center;font-size:20px;">
                    <i class="fa-solid fa-store-check"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Beroperasi Aktif</p>
                    <h3 class="fw-bold mb-0 text-success" style="font-size:24px;">{{ $activeStores }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:var(--danger-50);color:var(--danger);display:flex;align-items:center;justify-content:center;font-size:20px;">
                    <i class="fa-solid fa-store-slash"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Tutup / Nonaktif</p>
                    <h3 class="fw-bold mb-0 text-danger" style="font-size:24px;">{{ $inactiveStores }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

@if($stores->isEmpty())
<div class="card empty-state shadow-sm border-0" style="border-radius:var(--radius-lg);">
    <div class="card-body text-center py-5">
        <div class="empty-state-icon mb-3 mx-auto" style="width:80px;height:80px;background:var(--neutral-100);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size: 2.5rem; color: var(--text-muted);">
            <i class="fa-solid fa-store-slash"></i>
        </div>
        <h4 class="fw-bold text-dark">Belum Ada Cabang Toko</h4>
        <p class="text-muted mb-4" style="font-size:14px; max-width:400px; margin:0 auto;">Anda belum mendaftarkan cabang toko mana pun. Tambahkan cabang pertama Anda untuk mulai mengatur operasional kasir.</p>
        <a href="{{ route('admin.stores.create') }}" class="btn btn-primary px-4 py-2" style="font-weight:600;"><i class="fa-solid fa-plus me-2"></i> Tambah Toko Sekarang</a>
    </div>
</div>
@else
<div class="card stagger-1 shadow-sm border-0 mb-5" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0" style="font-size:16px;">Daftar Seluruh Toko</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover align-middle" style="margin:0; min-width:800px;">
                <thead>
                    <tr>
                        <th style="width:350px;">Informasi Toko</th>
                        <th style="width:180px;">Kode Cabang</th>
                        <th style="width:150px;">Telepon</th>
                        <th style="width:120px;">Status</th>
                        <th class="text-end" style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stores as $s)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="store-initials">{{ strtoupper(substr($s->name, 0, 1)) }}</div>
                                <div style="min-width: 0;">
                                    <a href="{{ route('admin.stores.show', $s->id) }}" class="fw-bold text-primary text-decoration-none d-block text-truncate mb-1" style="font-size:15px;">{{ $s->name }}</a>
                                    <div class="text-muted text-truncate" style="font-size:12px; max-width:250px;" title="{{ $s->address }}">
                                        <i class="fa-solid fa-location-dot me-1"></i>{{ $s->address }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size:13px; letter-spacing:1px;">{{ $s->code }}</span>
                        </td>
                        <td>
                            <span class="text-dark fw-semibold" style="font-size:14px;">{{ $s->phone ?? '-' }}</span>
                        </td>
                        <td>
                            @if($s->is_active)
                                <span class="badge badge-success rounded-pill px-2 py-1" style="font-size:11px; font-weight:600;"><i class="fa-solid fa-check-circle me-1"></i> Aktif</span>
                            @else
                                <span class="badge badge-danger rounded-pill px-2 py-1" style="font-size:11px; font-weight:600;"><i class="fa-solid fa-circle-xmark me-1"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-3" style="border:1px solid var(--border-light);background:white;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;" onclick="this.nextElementSibling.classList.toggle('show')">
                                    <i class="fa-solid fa-ellipsis-vertical text-secondary"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="min-width: 160px;">
                                    <a href="{{ route('admin.stores.show', $s->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-eye me-2 w-15px text-center"></i> Detail Cabang</a>
                                    <a href="{{ route('admin.stores.edit', $s->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-pen-to-square me-2 w-15px text-center"></i> Edit Informasi</a>
                                    <div class="dropdown-divider my-1"></div>
                                    <form action="{{ route('admin.stores.destroy', $s->id) }}" method="POST" class="m-0" id="form-del-{{ $s->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="dropdown-item py-2 fw-semibold text-danger" style="font-size:14px;" onclick="confirmDelete({{ $s->id }}, '{{ addslashes($s->name) }}')">
                                            <i class="fa-regular fa-circle-xmark me-2 w-15px text-center"></i> Nonaktifkan
                                        </button>
                                    </form>
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
@endif

<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Nonaktifkan Toko?',
        html: `Cabang <strong>${name}</strong> akan ditandai sebagai nonaktif.<br>Histori transaksi dan laporan dari toko ini akan tetap dipertahankan dengan aman.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-power-off me-1"></i> Ya, Nonaktifkan',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); 
    });
}
</script>
@endsection