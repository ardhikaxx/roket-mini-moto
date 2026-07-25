
@extends("layouts.admin")
@section("title", "Manajemen Pengguna")
@section("content")
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Manajemen Pengguna (Karyawan & Kepala Toko)</h2>
        <button class="btn btn-primary" onclick="Swal.fire('Fitur Tambah Pengguna', 'Segera Hadir!', 'info')">+ Tambah Pengguna</button>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table datatable table-bordered">
                <thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>No. Telp</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach(\App\Models\User::all() as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($user->role) }}</span></td>
                        <td>{{ $user->phone ?? "-" }}</td>
                        <td><span class="badge {{ $user->is_active ? "bg-success" : "bg-danger" }}">{{ $user->is_active ? "Aktif" : "Nonaktif" }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info text-white">Edit</button>
                            <button class="btn btn-sm btn-warning text-dark">Reset PIN</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
