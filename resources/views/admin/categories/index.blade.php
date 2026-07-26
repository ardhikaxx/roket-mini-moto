@extends('layouts.admin')
@section('title', 'Kategori Produk')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Kategori</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div><h1 class="page-title">Kategori Produk</h1><p class="page-subtitle">Kelola kategori untuk mengelompokkan produk</p></div>
        <div class="page-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus-circle me-1"></i> Tambah Kategori</button>
        </div>
    </div>
</div>

@if($categories->isEmpty())
<div class="card empty-state shadow-sm border-0">
    <div class="card-body text-center p-5">
        <div class="empty-state-icon mb-3" style="font-size: 3rem; color: var(--text-muted);"><i class="fa-solid fa-tags"></i></div>
        <h4 class="fw-bold">Belum ada Kategori</h4>
        <p class="text-secondary">Tambahkan kategori untuk mengelompokkan produk Anda.</p>
        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus-circle me-1"></i> Tambah Kategori</button>
    </div>
</div>
@else
<div class="card stagger-1 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead style="background: var(--neutral-50);"><tr><th>ID</th><th>Kategori</th><th>Slug</th><th>Jumlah Produk</th><th class="cell-action text-end">Aksi</th></tr></thead>
                <tbody>
                    @foreach($categories as $c)
                    <tr class="align-middle">
                        <td style="color:var(--text-secondary);">#{{ $c->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:var(--radius-sm);background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-tag"></i></div>
                                <span class="fw-semibold" style="font-size:15px;color:var(--text);">{{ $c->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary);font-size:13px;font-family:monospace;">{{ $c->slug }}</td>
                        <td><span class="badge badge-neutral bg-light text-dark border">{{ $c->products_count }} produk</span></td>
                        <td class="cell-action text-end">
                            <button class="btn btn-light btn-icon-sm rounded-circle me-1" onclick="editCategory({{ $c->id }}, '{{ $c->name }}')" title="Edit"><i class="fa-regular fa-pen-to-square text-primary"></i></button>
                            <form action="{{ route('admin.categories.destroy', $c->id) }}" method="POST" class="d-inline" id="form-del-{{ $c->id }}">@csrf @method('DELETE')
                                <button type="button" class="btn btn-light btn-icon-sm rounded-circle" onclick="confirmDelete({{ $c->id }}, '{{ $c->name }}')" title="Hapus"><i class="fa-regular fa-trash-can text-danger"></i></button>
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

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
      <form action="{{ route('admin.categories.store') }}" method="POST">
          @csrf
          <div class="modal-header d-flex align-items-center">
              <h5 class="modal-title mb-0"><i class="fa-solid fa-folder-plus text-primary me-2"></i>Tambah Kategori</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="form-group mb-0"><label class="form-label">Nama Kategori <span class="required">*</span></label><input type="text" name="name" class="form-control" required placeholder="Contoh: Helm, Oli, Apparel"></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan</button></div>
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
          <div class="modal-body">
              <div class="form-group mb-0"><label class="form-label">Nama Kategori <span class="required">*</span></label><input type="text" name="name" id="editName" class="form-control" required></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-1"></i> Update</button></div>
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
    Swal.fire({title:'Hapus Kategori?',text:'Kategori "'+name+'" akan dihapus. Pastikan tidak ada produk di dalamnya.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); });
}
</script>
@endsection
