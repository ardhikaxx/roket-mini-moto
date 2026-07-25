@extends('layouts.admin')
@section('title', 'Manajemen Kategori')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <h2 class="fw-bold">Kategori Produk</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Tambah Kategori</button>
</div>
<div class="card shadow-sm p-4 border-0">
    <table class="table datatable table-bordered">
        <thead><tr><th>ID</th><th>Nama Kategori</th><th>Slug</th><th>Jumlah Produk</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->products_count }}</td>
                <td>
                    <button class="btn btn-sm btn-info text-white" onclick="editCategory({{ $category->id }}, '{{ $category->name }}')">Edit</button>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" id="form-delete-{{ $category->id }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirm({{ $category->id }})">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.categories.store') }}" method="POST">
          @csrf
          <div class="modal-header"><h5 class="modal-title">Tambah Kategori</h5></div>
          <div class="modal-body">
              <label>Nama Kategori</label>
              <input type="text" name="name" class="form-control" required>
          </div>
          <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm" method="POST">
          @csrf @method('PUT')
          <div class="modal-header"><h5 class="modal-title">Edit Kategori</h5></div>
          <div class="modal-body">
              <label>Nama Kategori</label>
              <input type="text" name="name" id="editName" class="form-control" required>
          </div>
          <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
      </form>
    </div>
  </div>
</div>

<script>
function editCategory(id, name) {
    document.getElementById('editForm').action = '/admin/categories/' + id;
    document.getElementById('editName').value = name;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
function deleteConfirm(id) {
    Swal.fire({
        title: 'Yakin hapus kategori?', text: "Data tidak bisa dikembalikan!", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
    }).then((result) => { if (result.isConfirmed) document.getElementById('form-delete-'+id).submit(); })
}
</script>
@endsection