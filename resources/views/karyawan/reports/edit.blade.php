@extends('layouts.admin')
@section('title', 'Perbaiki Laporan')
@section('breadcrumb')
    <a href="{{ route('karyawan.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('karyawan.reports.index') }}">Laporan Saya</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Perbaiki Laporan #{{ $report->id }}</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Perbaiki Laporan #{{ $report->id }}</h1>
            <p class="page-subtitle">Laporan sebelumnya ditolak. Silakan perbaiki data dan kirim ulang.</p>
        </div>
    </div>
</div>

@if($report->rejection_reason)
<div class="alert alert-danger mb-4">
    <div class="alert-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
    <div class="alert-content">
        <strong>Alasan Penolakan:</strong> {{ $report->rejection_reason }}
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('karyawan.reports.update', $report->id) }}" method="POST" enctype="multipart/form-data" id="reportForm">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-row mb-4">
                        <div class="form-group">
                            <label class="form-label">Pilih Toko <span class="required">*</span></label>
                            <select name="store_id" class="form-select" required>
                                @foreach($stores as $s) <option value="{{ $s->id }}" {{ $report->store_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3">Produk Terjual</h5>
                    <div class="table-container mb-3">
                        <table class="table" style="margin:0;">
                            <thead><tr><th>Produk</th><th width="120">Qty</th><th width="80">Aksi</th></tr></thead>
                            <tbody id="productRows">
                                @foreach($report->items as $i => $item)
                                <tr>
                                    <td>
                                        <select name="products[{{ $i }}][id]" class="form-select" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $p) <option value="{{ $p->id }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }} (Rp {{ number_format($p->price,0,',','.') }})</option> @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="products[{{ $i }}][qty]" class="form-control" min="1" value="{{ $item->quantity }}" required></td>
                                    <td><button type="button" class="btn btn-ghost btn-icon-sm text-danger" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash-can"></i></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm mb-4" onclick="addRow()"><i class="fa-solid fa-plus"></i> Tambah Produk Lain</button>

                    <h5 class="fw-bold mb-3">Foto Bukti</h5>
                    @if($report->images->count() > 0)
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-bottom:16px;">
                        @foreach($report->images as $img)
                        <div style="border-radius:var(--radius-md);overflow:hidden;aspect-ratio:1;background:var(--neutral-100);position:relative;">
                            <img src="{{ asset('storage/'.$img->image_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            <form action="{{ route('karyawan.reports.delete-image', $img->id) }}" method="POST" style="position:absolute;top:4px;right:4px;">@csrf @method('DELETE')
                                <button type="submit" style="width:24px;height:24px;border-radius:50%;background:rgba(0,0,0,0.6);color:#fff;border:none;font-size:10px;cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <div class="form-group mb-4">
                        <label class="form-label">Tambah Foto Baru</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $report->notes) }}</textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card" style="position:sticky;top:84px;">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Aksi</h5>
                            <button type="submit" class="btn btn-primary w-100 mb-2 py-3 fw-bold" id="submitBtn"><i class="fa-solid fa-paper-plane"></i> Kirim Ulang Laporan</button>
                            <a href="{{ route('karyawan.reports.index') }}" class="btn btn-secondary w-100">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let rowIdx = {{ $report->items->count() }};
function addRow() {
    const tbody = document.getElementById('productRows');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="products[\${rowIdx}][id]" class="form-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price,0,',','.') }})</option> @endforeach
            </select>
        </td>
        <td><input type="number" name="products[\${rowIdx}][qty]" class="form-control" min="1" value="1" required></td>
        <td><button type="button" class="btn btn-ghost btn-icon-sm text-danger" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash-can"></i></button></td>
    `;
    tbody.appendChild(tr);
    rowIdx++;
}
document.getElementById('reportForm').addEventListener('submit', function(e) {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';
});
</script>
@endsection
