@extends('layouts.admin')
@section('title', 'Manajemen Toko')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <h2 class="fw-bold">Manajemen Toko / Cabang</h2>
    <a href="{{ route('admin.stores.create') }}" class="btn btn-primary">+ Tambah Toko</a>
</div>
<div class="card shadow-sm p-4 border-0">
    <table class="table datatable table-bordered">
        <thead><tr><th>Kode</th><th>Nama Toko</th><th>Alamat</th><th>No Telp</th><th>Pegawai</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($stores as $s)
            <tr>
                <td>{{ $s->code }}</td>
                <td>{{ $s->name }}</td>
                <td>{{ $s->address }}</td>
                <td>{{ $s->phone ?? '-' }}</td>
                <td>{{ $s->users_count }}</td>
                <td><span class="badge {{ $s->is_active ? 'bg-success' : 'bg-danger' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td>
                    <a href="{{ route('admin.stores.edit', $s->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                    <form action="{{ route('admin.stores.destroy', $s->id) }}" method="POST" class="d-inline" id="form-delete-{{ $s->id }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirm({{ $s->id }})">Nonaktifkan</button>
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
        title: 'Nonaktifkan toko?', text: "Data histori toko tetap ada namun disembunyikan dari transaksi baru.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya!'
    }).then((r) => { if (r.isConfirmed) document.getElementById('form-delete-'+id).submit(); })
}
</script>
@endsection