@extends('layouts.admin')
@section('title', 'Kategori Produk')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Kategori</span>
@endsection
@section('content')

@php
    $totalCategories = $categories->count();
    $totalProducts = $categories->sum('products_count');
    $emptyCategories = $categories->where('products_count', 0)->count();
@endphp

<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-layer-group text-primary me-2"></i>Kategori Produk</h1>
            <p class="page-subtitle">Kelola kategori untuk mengelompokkan produk secara terstruktur</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addModal" style="font-weight:600;box-shadow:0 4px 12px rgba(230,57,70,0.25);"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Kategori</button>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body d-flex align-items-center p-4">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;"><i class="fa-solid fa-layer-group"></i></div>
                <div class="ms-3">
                    <p class="text-muted mb-0" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Kategori</p>
                    <h3 class="fw-bold mb-0 mt-1" style="font-size:26px;color:var(--text);">{{ $totalCategories }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body d-flex align-items-center p-4">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--success-50);color:var(--success-600);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;"><i class="fa-solid fa-box"></i></div>
                <div class="ms-3">
                    <p class="text-muted mb-0" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Produk</p>
                    <h3 class="fw-bold mb-0 mt-1" style="font-size:26px;color:var(--text);">{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body d-flex align-items-center p-4">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--warning-50);color:var(--warning-700);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;"><i class="fa-solid fa-folder-minus"></i></div>
                <div class="ms-3">
                    <p class="text-muted mb-0" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Kategori Kosong</p>
                    <h3 class="fw-bold mb-0 mt-1" style="font-size:26px;color:var(--text);">{{ $emptyCategories }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

@if($categories->isEmpty())
<div class="card empty-state shadow-sm border-0" style="border-radius:var(--radius-lg);">
    <div class="card-body text-center p-5 my-4">
        <div class="empty-state-icon mb-3 mx-auto" style="width:80px;height:80px;border-radius:50%;background:var(--neutral-100);color:var(--text-muted);display:flex;align-items:center;justify-content:center;font-size: 2.5rem;"><i class="fa-solid fa-tags"></i></div>
        <h4 class="fw-bold mb-2">Belum ada Kategori</h4>
        <p class="text-secondary mb-4">Tambahkan kategori pertama Anda untuk mulai mengelompokkan produk dengan rapi.</p>
        <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#addModal" style="font-weight:600;"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Kategori Sekarang</button>
    </div>
</div>
@else
<div class="card shadow-sm border-0 mb-5" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover" style="margin:0; border-collapse: separate; border-spacing: 0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:80px;">ID</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Kategori & Slug</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Jumlah Produk</th>
                        <th class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $c)
                    <tr class="align-middle" style="transition: all 0.2s;">
                        <td style="padding:16px 24px; color:var(--text-secondary); font-size:13px; font-weight:600; border-bottom:1px solid var(--neutral-100);">#{{ $c->id }}</td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:16px;"><i class="fa-solid fa-tag"></i></div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:15px;"><a href="{{ route('admin.categories.show', $c->id) }}" class="text-decoration-none text-dark">{{ $c->name }}</a></div>
                                    <div style="color:var(--text-secondary);font-size:12px;font-family:monospace;margin-top:2px;">{{ $c->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill" style="background:{{ $c->products_count > 0 ? 'var(--success-50)' : 'var(--neutral-100)' }}; color:{{ $c->products_count > 0 ? 'var(--success-700)' : 'var(--text-secondary)' }}; border:1px solid {{ $c->products_count > 0 ? 'var(--success-200)' : 'var(--border-light)' }}; font-size:13px; font-weight:600;">
                                <i class="fa-solid {{ $c->products_count > 0 ? 'fa-box' : 'fa-box-open' }} me-2"></i> {{ $c->products_count }} Produk
                            </div>
                        </td>
                        <td class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.categories.show', $c->id) }}" class="btn btn-light btn-sm text-secondary" title="Lihat Produk" style="border:1px solid var(--border-light); font-weight:600; background:white;"><i class="fa-solid fa-eye me-1"></i> Detail</a>
                                <button class="btn btn-light btn-sm text-primary" onclick="editCategory({{ $c->id }}, '{{ $c->name }}')" title="Edit Kategori" style="border:1px solid var(--border-light); font-weight:600; background:white;"><i class="fa-regular fa-pen-to-square me-1"></i> Edit</button>
                                <form action="{{ route('admin.categories.destroy', $c->id) }}" method="POST" class="d-inline" id="form-del-{{ $c->id }}">@csrf @method('DELETE')
                                    <button type="button" class="btn btn-light btn-sm text-danger" onclick="confirmDelete({{ $c->id }}, '{{ $c->name }}')" title="Hapus Kategori" style="border:1px solid var(--border-light); font-weight:600; background:white;"><i class="fa-regular fa-trash-can me-1"></i> Hapus</button>
                                </form>
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

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
      <form action="{{ route('admin.categories.store') }}" method="POST">
          @csrf
          <div class="modal-header d-flex align-items-center">
              <h5 class="modal-title mb-0"><i class="fa-solid fa-folder-plus text-primary me-2"></i>Tambah Kategori</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="form-group mb-0">
                  <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control form-control-lg" required placeholder="Contoh: Helm, Oli, Apparel">
                  <div class="form-text mt-2">Slug akan di-generate otomatis berdasarkan nama kategori.</div>
              </div>
          </div>
          <div class="modal-footer px-4 pb-4 pt-0 border-0 bg-white">
              <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="font-weight:600;">Batal</button>
              <button type="submit" class="btn btn-primary px-4" style="font-weight:600;"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kategori</button>
          </div>
      </form>
  </div></div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
      <form id="editForm" method="POST">
          @csrf @method('PUT')
          <div class="modal-header d-flex align-items-center">
              <h5 class="modal-title mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Kategori</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="form-group mb-0">
                  <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="editName" class="form-control form-control-lg" required>
              </div>
          </div>
          <div class="modal-footer px-4 pb-4 pt-0 border-0 bg-white">
              <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="font-weight:600;">Batal</button>
              <button type="submit" class="btn btn-primary px-4" style="font-weight:600;"><i class="fa-solid fa-check me-1"></i> Simpan Perubahan</button>
          </div>
      </form>
  </div></div>
</div>

<script>
function editCategory(id, name) {
    document.getElementById('editForm').action = '/admin/categories/' + id;
    document.getElementById('editName').value = name;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: 'Kategori "' + name + '" akan dihapus. Pastikan tidak ada produk di dalamnya.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if (r.isConfirmed) document.getElementById('form-del-'+id).submit(); 
    });
}
</script>
@endsection
