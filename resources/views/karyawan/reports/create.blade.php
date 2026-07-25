@extends('layouts.admin')
@section('title', 'Buat Laporan Penjualan')
@section('content')
<h2 class="fw-bold mb-4">Buat Laporan Penjualan</h2>
<div class="card shadow-sm p-4 border-0">
    <form action="{{ route('karyawan.reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Pilih Toko <span class="text-danger">*</span></label>
                <select name="store_id" class="form-select" required>
                    <option value="">-- Pilih Toko Tempat Anda Bekerja --</option>
                    @foreach($stores as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                </select>
            </div>
            
            <div class="col-12 mb-3">
                <label class="fw-bold">Produk Terjual <span class="text-danger">*</span></label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="productTable">
                        <thead>
                            <tr class="bg-light">
                                <th>Produk</th>
                                <th width="150">Jumlah (Qty)</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            <tr>
                                <td>
                                    <select name="products[0][id]" class="form-select" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price,0,',','.') }})</option> @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="products[0][qty]" class="form-control" min="1" value="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger text-white mt-1" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-success" onclick="addRow()">+ Tambah Produk Lain</button>
            </div>

            <div class="col-md-12 mb-3">
                <label>Foto Bukti Penjualan (Max 10 foto) <span class="text-danger">*</span></label>
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                <small class="text-muted">Gunakan tombol CTRL / tahan untuk memilih beberapa foto sekaligus.</small>
            </div>

            <div class="col-md-12 mb-3">
                <label>Catatan / Keterangan</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Pembayaran transfer BCA a.n Budi"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3 w-100 py-2 fw-bold">KIRIM LAPORAN PENJUALAN</button>
    </form>
</div>

<script>
let rowIdx = 1;
function addRow() {
    const tbody = document.getElementById('productRows');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="products[${rowIdx}][id]" class="form-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price,0,',','.') }})</option> @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="products[${rowIdx}][qty]" class="form-control" min="1" value="1" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger text-white mt-1" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    rowIdx++;
}
</script>
@endsection