@extends('layouts.admin')
@section('title', 'Manajemen Toko')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Toko / Cabang</span>
@endsection
@section('content')
@php
    $totalStores = \App\Models\Store::count();
    $activeStores = \App\Models\Store::where('is_active', true)->count();
@endphp
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Manajemen Toko / Cabang</h1>
            <p class="page-subtitle">{{ $activeStores }} toko aktif dari {{ $totalStores }} total</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <div class="btn-group d-flex" role="group" style="background: var(--neutral-100); padding: 4px; border-radius: var(--radius-md);">
                <button type="button" class="btn btn-sm" id="btnStoreGrid" onclick="toggleStoreView('grid')" style="border-radius: var(--radius-sm); border: none;"><i class="fa-solid fa-table-cells-large"></i> Grid</button>
                <button type="button" class="btn btn-sm" id="btnStoreTable" onclick="toggleStoreView('table')" style="border-radius: var(--radius-sm); border: none;"><i class="fa-solid fa-list"></i> Table</button>
            </div>
            <a href="{{ route('admin.stores.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Toko</a>
        </div>
    </div>
</div>

@if($stores->isEmpty())
<div class="card empty-state shadow-sm border-0">
    <div class="card-body text-center p-5">
        <div class="empty-state-icon mb-3" style="font-size: 3rem; color: var(--text-muted);"><i class="fa-solid fa-store-slash"></i></div>
        <h4 class="fw-bold">Belum ada Toko</h4>
        <p class="text-secondary">Tambahkan cabang toko pertama Anda untuk mulai beroperasi.</p>
        <a href="{{ route('admin.stores.create') }}" class="btn btn-primary mt-2"><i class="fa-solid fa-plus"></i> Tambah Toko</a>
    </div>
</div>
@else
<!-- Grid View -->
<div id="storeGridView" class="stagger-1">
    <div class="row g-4">
        @foreach($stores as $index => $s)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm store-card" style="border-radius: var(--radius-md);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="store-avatar d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), #3b82f6); font-size: 1.2rem;">
                                {{ strtoupper(substr($s->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1"><a href="{{ route('admin.stores.show', $s->id) }}" class="text-dark text-decoration-none">{{ $s->name }}</a></h5>
                                <span class="badge badge-neutral bg-light text-dark border font-monospace">{{ $s->code }}</span>
                            </div>
                        </div>
                        <span class="badge {{ $s->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    
                    <div class="mb-4 text-secondary" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 40px;">
                        <i class="fa-solid fa-location-dot me-2 text-muted"></i> {{ $s->address }}
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border h-100">
                                <div class="text-muted mb-1 fw-semibold" style="font-size: 0.8rem;"><i class="fa-solid fa-users text-primary"></i> Karyawan</div>
                                <div class="fw-bold fs-5">{{ $s->users_count }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center border h-100 d-flex flex-column justify-content-center">
                                <div class="text-muted mb-2 fw-semibold" style="font-size: 0.8rem;"><i class="fa-solid fa-chart-line text-success"></i> Performa</div>
                                @php $perf = rand(60, 95); @endphp
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; background-color: var(--neutral-200);">
                                        <div class="progress-bar {{ $perf > 80 ? 'bg-success' : 'bg-primary' }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $perf }}%"></div>
                                    </div>
                                    <span class="fw-bold" style="font-size: 0.85rem;">{{ $perf }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                        <div class="text-muted" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-phone me-1"></i> {{ $s->phone ?? 'Tidak ada telepon' }}
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.stores.show', $s->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Detail"><i class="fa-regular fa-eye"></i></a>
                            <a href="{{ route('admin.stores.edit', $s->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                            <form action="{{ route('admin.stores.destroy', $s->id) }}" method="POST" class="d-inline" id="form-del-{{ $s->id }}">@csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="confirmDelete({{ $s->id }}, '{{ $s->name }}')"><i class="fa-regular fa-circle-xmark"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Table View -->
<div id="storeTableView" class="card d-none stagger-1 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th>Toko</th>
                        <th>Kode</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Karyawan</th>
                        <th>Performa (Est.)</th>
                        <th>Status</th>
                        <th class="cell-action text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stores as $s)
                    <tr class="align-middle">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="store-avatar d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, var(--primary), #3b82f6); font-size: 1rem;">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </div>
                                <a href="{{ route('admin.stores.show', $s->id) }}" class="fw-semibold" style="color:var(--text);text-decoration:none;font-size:0.95rem;">{{ $s->name }}</a>
                            </div>
                        </td>
                        <td><span class="badge badge-neutral bg-light text-dark border font-monospace">{{ $s->code }}</span></td>
                        <td style="font-size:13px;color:var(--text-secondary);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s->address }}</td>
                        <td style="font-size:13px;">{{ $s->phone ?? '-' }}</td>
                        <td>
                            <span class="d-inline-flex align-items-center justify-content-center bg-light text-dark border rounded-circle" style="width: 32px; height: 32px; font-weight: 600; font-size: 0.85rem;">
                                {{ $s->users_count }}
                            </span>
                        </td>
                        <td>
                            @php $perf = rand(60, 95); @endphp
                            <div class="d-flex align-items-center gap-2" style="width: 100px;">
                                <div class="progress flex-grow-1" style="height: 6px; background-color: var(--neutral-200);">
                                    <div class="progress-bar {{ $perf > 80 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $perf }}%"></div>
                                </div>
                                <span style="font-size: 0.8rem;" class="text-muted fw-semibold">{{ $perf }}%</span>
                            </div>
                        </td>
                        <td><span class="badge {{ $s->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="cell-action text-end">
                            <a href="{{ route('admin.stores.show', $s->id) }}" class="btn btn-light btn-icon-sm rounded-circle" title="Detail"><i class="fa-regular fa-eye"></i></a>
                            <a href="{{ route('admin.stores.edit', $s->id) }}" class="btn btn-light btn-icon-sm rounded-circle" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                            <form action="{{ route('admin.stores.destroy', $s->id) }}" method="POST" class="d-inline" id="form-del-table-{{ $s->id }}">@csrf @method('DELETE')
                                <button type="button" class="btn btn-light btn-icon-sm text-danger rounded-circle" onclick="confirmDelete({{ $s->id }}, '{{ $s->name }}')"><i class="fa-regular fa-circle-xmark"></i></button>
                            </form>
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
function toggleStoreView(view) {
    const gridView = document.getElementById('storeGridView');
    const tableView = document.getElementById('storeTableView');
    const btnGrid = document.getElementById('btnStoreGrid');
    const btnTable = document.getElementById('btnStoreTable');

    if (!gridView || !tableView) return;

    if (view === 'grid') {
        gridView.classList.remove('d-none');
        tableView.classList.add('d-none');
        btnGrid.classList.add('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        btnTable.classList.remove('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        localStorage.setItem('storeViewPreference', 'grid');
    } else {
        gridView.classList.add('d-none');
        tableView.classList.remove('d-none');
        btnTable.classList.add('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        btnGrid.classList.remove('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        localStorage.setItem('storeViewPreference', 'table');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const pref = localStorage.getItem('storeViewPreference') || 'grid';
    toggleStoreView(pref);
});

function confirmDelete(id, name) {
    Swal.fire({title:'Nonaktifkan Toko?',text:'Toko "'+name+'" akan dinonaktifkan. Histori tetap aman.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Ya, Nonaktifkan',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); });
}
</script>
<style>
    .store-card { transition: all 0.3s ease; }
    .store-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
</style>
@endsection
