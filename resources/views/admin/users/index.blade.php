@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <h2 class="fw-bold">Manajemen Pengguna</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
</div>
<div class="card shadow-sm p-4 border-0">
    <table class="table datatable table-bordered">
        <thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Cabang (Toko)</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->username }}</td>
                <td><span class="badge bg-secondary">{{ strtoupper($u->role) }}</span></td>
                <td>
                    @if($u->isAdmin()) <span class="badge bg-primary">Akses Global</span>
                    @else
                        @foreach($u->stores as $st) <span class="badge bg-info">{{ $st->name }}</span> @endforeach
                    @endif
                </td>
                <td><span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td>
                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" id="form-delete-{{ $u->id }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirm({{ $u->id }})">Nonaktifkan</button>
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
        title: 'Nonaktifkan akun?', text: "Pengguna ini tidak akan bisa login lagi.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya!'
    }).then((r) => { if (r.isConfirmed) document.getElementById('form-delete-'+id).submit(); })
}
</script>
@endsection