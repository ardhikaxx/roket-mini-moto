@extends('layouts.admin')
@section('title', 'Manajemen Produk')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <h2 class="fw-bold">Manajemen Produk</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
</div>
<div class="card shadow-sm p-4 border-0">
    <table class="table datatable table-bordered">
        <thead><tr><th>Foto</th><th>SKU</th><th>Nama</th><th>Kategori</th><th>Harga Jual</th><th>Stok</th><th>Status</th><th>Landing Page</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td><img src="{{ $p->photo ? asset('storage/'.$p->photo) : asset('assets/images/default.jpg') }}" width="50" height="50" class="rounded object-fit-cover"></td>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category?->name ?? '-' }}</td>
                <td>Rp {{ number_format($p->price,0,',','.') }}</td>
                <td>{{ $p->stock }} {{ $p->unit }}</td>
                <td><span class="badge {{ $p->is_active ? 'bg-success' : 'bg-danger' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td><span class="badge {{ $p->show_on_landing ? 'bg-info' : 'bg-secondary' }}">{{ $p->show_on_landing ? 'Tampil' : 'Sembunyi' }}</span></td>
                <td>
                    <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                    <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" class="d-inline" id="form-delete-{{ $p->id }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirm({{ $p->id }})">Nonaktifkan</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
function deleteConfirm(id) {
    Swal.fire({
        title: 'Nonaktifkan produk?', text: "Produk akan disembunyikan dari sistem (Soft Delete).", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya!'
    }).then((r) => { if (r.isConfirmed) document.getElementById('form-delete-'+id).submit(); })
}
</script>
@endsection