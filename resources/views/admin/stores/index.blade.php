@extends('layouts.admin')
@section('title', 'Manajemen Toko')
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
<div class="card stagger-1 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th>Toko</th>
                        <th>Kode</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th class="cell-action text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stores as $s)
                    <tr class="align-middle">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, var(--primary), #3b82f6); font-size: 1rem;">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </div>
                                <a href="{{ route('admin.stores.show', $s->id) }}" class="fw-semibold" style="color:var(--text);text-decoration:none;font-size:0.95rem;">{{ $s->name }}</a>
                            </div>
                        </td>
                        <td><span class="badge badge-neutral bg-light text-dark border font-monospace">{{ $s->code }}</span></td>
                        <td style="font-size:13px;color:var(--text-secondary);max-width:250px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s->address }}</td>
                        <td style="font-size:13px;">{{ $s->phone ?? '-' }}</td>
                        <td><span class="badge {{ $s->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="cell-action text-end">
                            <a href="{{ route('admin.stores.show', $s->id) }}" class="btn btn-light btn-icon-sm rounded-circle" title="Detail"><i class="fa-regular fa-eye"></i></a>
                            <a href="{{ route('admin.stores.edit', $s->id) }}" class="btn btn-light btn-icon-sm rounded-circle" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                            <form action="{{ route('admin.stores.destroy', $s->id) }}" method="POST" class="d-inline" id="form-del-{{ $s->id }}">@csrf @method('DELETE')
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
function confirmDelete(id, name) {
    Swal.fire({title:'Nonaktifkan Toko?',text:'Toko "'+name+'" akan dinonaktifkan. Histori tetap aman.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Ya, Nonaktifkan',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); });
}
</script>
@endsection