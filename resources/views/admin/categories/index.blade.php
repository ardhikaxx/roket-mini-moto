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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus"></i> Tambah Kategori</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead><tr><th>ID</th><th>Nama Kategori</th><th>Slug</th><th>Jumlah Produk</th><th class="cell-action">Aksi</th></tr></thead>
                <tbody>
                    @foreach($categories as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td class="fw-semibold">{{ $c->name }}</td>
                        <td style="color:var(--text-secondary);font-size:13px;">{{ $c->slug }}</td>
                        <td><span class="badge badge-neutral">{{ $c->products_count }} produk</span></td>
                        <td class="cell-action">
                            <button class="btn btn-ghost btn-icon-sm" onclick="editCategory({{ $c->id }}, '{{ $c->name }}')"><i class="fa-regular fa-pen-to-square"></i></button>
                            <form action="{{ route('admin.categories.destroy', $c->id) }}" method="POST" class="d-inline" id="form-del-{{ $c->id }}">@csrf @method('DELETE')
                                <button type="button" class="btn btn-ghost btn-icon-sm text-danger" onclick="confirmDelete({{ $c->id }}, '{{ $c->name }}')"><i class="fa-regular fa-trash-can"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
      <form action="{{ route('admin.categories.store') }}" method="POST">
          @csrf
          <div class="modal-header"><h5 class="modal-title">Tambah Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
              <div class="form-group"><label class="form-label">Nama Kategori</label><input type="text" name="name" class="form-control" required></div>
          </div>
          <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
      </form>
  </div></div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
      <form id="editForm" method="POST">
          @csrf @method('PUT')
          <div class="modal-header"><h5 class="modal-title">Edit Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
              <div class="form-group"><label class="form-label">Nama Kategori</label><input type="text" name="name" id="editName" class="form-control" required></div>
          </div>
          <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
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
