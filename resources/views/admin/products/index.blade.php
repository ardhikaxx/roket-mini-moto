
@extends("layouts.admin")
@section("title", "Manajemen Produk")
@section("content")
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Manajemen Produk</h2>
        <button class="btn btn-primary" onclick="Swal.fire('Fitur Tambah Produk', 'Segera Hadir!', 'info')">+ Tambah Produk</button>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table datatable table-bordered">
                <thead><tr><th>ID</th><th>SKU</th><th>Nama</th><th>Kategori</th><th>Harga Jual</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach(\App\Models\Product::with("category")->get() as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? "-" }}</td>
                        <td>Rp {{ number_format($product->price,0,",",".") }}</td>
                        <td>{{ $product->stock }} {{ $product->unit }}</td>
                        <td><span class="badge {{ $product->is_active ? "bg-success" : "bg-danger" }}">{{ $product->is_active ? "Aktif" : "Nonaktif" }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info text-white">Edit</button>
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
