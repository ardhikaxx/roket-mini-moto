
@extends("layouts.admin")
@section("title", "Manajemen Toko")
@section("content")
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Manajemen Toko</h2>
        <button class="btn btn-primary" onclick="Swal.fire('Fitur Tambah Toko', 'Segera Hadir!', 'info')">+ Tambah Toko</button>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table datatable table-bordered">
                <thead><tr><th>Kode</th><th>Nama Toko</th><th>No. Telp</th><th>Alamat</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach(\App\Models\Store::all() as $store)
                    <tr>
                        <td>{{ $store->code }}</td>
                        <td>{{ $store->name }}</td>
                        <td>{{ $store->phone }}</td>
                        <td>{{ $store->address }}</td>
                        <td><span class="badge {{ $store->is_active ? "bg-success" : "bg-danger" }}">{{ $store->is_active ? "Aktif" : "Nonaktif" }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info text-white">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
