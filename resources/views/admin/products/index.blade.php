@extends('layouts.admin')
@section('title', 'Manajemen Produk')
@section('content')
@php
    $totalProducts = \App\Models\Product::count();
    $activeProducts = \App\Models\Product::where('is_active', true)->count();
    $inactiveProducts = \App\Models\Product::where('is_active', false)->count();
    $landingProducts = \App\Models\Product::where('show_on_landing', true)->count();
@endphp
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Manajemen Produk</h1>
            <p class="page-subtitle">Kelola seluruh produk bisnis Anda</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Produk</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 stagger-1">
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:default;"><div class="stat-label text-secondary fw-semibold mb-1">Total Produk</div><div class="stat-value" style="font-size:28px;">{{ $totalProducts }}</div></div></div>
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:default;"><div class="stat-label text-secondary fw-semibold mb-1">Aktif</div><div class="stat-value" style="font-size:28px;color:var(--success);">{{ $activeProducts }}</div></div></div>
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:default;"><div class="stat-label text-secondary fw-semibold mb-1">Nonaktif</div><div class="stat-value" style="font-size:28px;color:var(--danger);">{{ $inactiveProducts }}</div></div></div>
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:default;"><div class="stat-label text-secondary fw-semibold mb-1">Tampil di Landing</div><div class="stat-value" style="font-size:28px;color:var(--info);">{{ $landingProducts }}</div></div></div>
</div>

@if($products->isEmpty())
<div class="card empty-state shadow-sm border-0">
    <div class="card-body text-center p-5">
        <div class="empty-state-icon mb-3" style="font-size: 3rem; color: var(--text-muted);"><i class="fa-solid fa-box-open"></i></div>
        <h4 class="fw-bold">Belum ada Produk</h4>
        <p class="text-secondary">Mulai tambahkan produk pertama Anda untuk dijual.</p>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-2"><i class="fa-solid fa-plus"></i> Tambah Produk</a>
    </div>
</div>
@else
<div class="card stagger-1 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th style="width:60px;">Foto</th>
                        <th>Nama Produk</th>
                        <th>SKU</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Landing</th>
                        <th>Dibuat</th>
                        <th class="cell-action text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    <tr class="align-middle">
                        <td>
                            <div style="width:48px;height:48px;border-radius:var(--radius-sm);background:var(--neutral-100);overflow:hidden;box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <img src="{{ $p->photo ? asset('storage/'.$p->photo) : asset('assets/images/default.jpg') }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.show', $p->id) }}" class="fw-semibold d-block" style="color:var(--text);text-decoration:none;font-size:0.95rem;">{{ $p->name }}</a>
                            <span class="text-muted" style="font-size:0.8rem;">ID: {{ $p->id }}</span>
                        </td>
                        <td style="color:var(--text-secondary);font-size:13px;font-family:monospace;background:var(--neutral-50);padding:4px 8px;border-radius:4px;">{{ $p->sku }}</td>
                        <td><span class="badge badge-neutral bg-light text-dark border">{{ $p->category->name ?? '-' }}</span></td>
                        <td class="fw-semibold text-primary">Rp {{ number_format($p->price,0,',','.') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress" style="height: 6px; width: 50px; background-color: var(--neutral-200);">
                                    <div class="progress-bar {{ $p->stock < 10 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ min(100, $p->stock) }}%"></div>
                                </div>
                                <span style="font-size:0.85rem;" class="{{ $p->stock < 10 ? 'text-danger fw-bold' : '' }}">{{ $p->stock }} {{ $p->unit }}</span>
                            </div>
                        </td>
                        <td><span class="badge {{ $p->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td><span class="badge {{ $p->show_on_landing ? 'badge-info' : 'badge-neutral' }} rounded-pill">{{ $p->show_on_landing ? 'Tampil' : 'Sembunyi' }}</span></td>
                        <td style="font-size:13px;color:var(--text-secondary);">{{ $p->created_at->format('d/m/Y') }}</td>
                        <td class="cell-action text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-icon-sm rounded-circle" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <a href="{{ route('admin.products.show', $p->id) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="dropdown-item"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                    <div class="dropdown-divider"></div>
                                    @if($p->is_active)
                                    <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" id="form-del-{{ $p->id }}">@csrf @method('DELETE')
                                        <button type="button" class="dropdown-item text-danger" onclick="confirmDelete({{ $p->id }}, '{{ $p->name }}')"><i class="fa-regular fa-circle-xmark"></i> Nonaktifkan</button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.products.activate', $p->id) }}" method="POST" id="form-act-{{ $p->id }}">@csrf
                                        <button type="button" class="dropdown-item text-success" onclick="confirmActivate({{ $p->id }}, '{{ $p->name }}')"><i class="fa-regular fa-circle-check"></i> Aktifkan</button>
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
@endif

<script>
function confirmDelete(id, name) {
    Swal.fire({title:'Nonaktifkan Produk',text:name+' akan dinonaktifkan. Histori transaksi tetap aman.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Ya, Nonaktifkan',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); });
}
function confirmActivate(id, name) {
    Swal.fire({title:'Aktifkan Produk',text:name+' akan diaktifkan kembali.',icon:'question',showCancelButton:true,confirmButtonColor:'#16a34a',confirmButtonText:'Ya, Aktifkan',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('form-act-'+id).submit(); });
}
</script>
@endsection