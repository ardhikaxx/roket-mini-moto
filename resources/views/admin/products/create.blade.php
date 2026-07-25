@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.products.index') }}">Produk</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Tambah Produk</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Tambah Produk Baru</h1><p class="page-subtitle">Lengkapi informasi produk untuk menambah ke katalog</p></div></div></div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-3" style="font-size:15px;">Informasi Produk</h5>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Nama Produk <span class="required">*</span></label><input type="text" name="name" class="form-control @error('name') error @enderror" required value="{{ old('name') }}"></div>
                        <div class="form-group"><label class="form-label">SKU / Kode Produk <span class="required">*</span></label><input type="text" name="sku" class="form-control @error('sku') error @enderror" required value="{{ old('sku') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Kategori <span class="required">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $c) <option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Satuan <span class="required">*</span></label><input type="text" name="unit" class="form-control" required value="{{ old('unit', 'pcs') }}"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea></div>

                    <h5 class="fw-bold mb-3 mt-4" style="font-size:15px;">Harga & Stok</h5>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Harga Jual (Rp) <span class="required">*</span></label><input type="number" name="price" class="form-control" required min="0" value="{{ old('price') }}"></div>
                        <div class="form-group"><label class="form-label">Harga Modal (Rp)</label><input type="number" name="cost_price" class="form-control" min="0" value="{{ old('cost_price') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Stok Awal <span class="required">*</span></label><input type="number" name="stock" class="form-control" required min="0" value="{{ old('stock', 0) }}"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3" style="font-size:15px;">Media Produk</h5>
                    <div class="form-group">
                        <label class="form-label">Foto Produk (Max 2MB)</label>
                        <div class="upload-zone" id="uploadZone" onclick="document.getElementById('photoInput').click()">
                            <div class="upload-zone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="upload-zone-text">Klik untuk upload foto produk</div>
                            <input type="file" id="photoInput" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(event)">
                        </div>
                        <div id="photoPreview" style="display:none;margin-top:12px;border-radius:var(--radius-md);overflow:hidden;position:relative;">
                            <img id="previewImg" style="width:100%;height:180px;object-fit:cover;">
                            <button type="button" style="position:absolute;top:8px;right:8px;width:28px;height:28px;border-radius:50%;background:rgba(0,0,0,0.6);color:#fff;border:none;font-size:12px;cursor:pointer;" onclick="removePhoto()"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 mt-4" style="font-size:15px;">Status & Publikasi</h5>
                    <div class="form-group">
                        <label class="form-switch mb-3">
                            <input type="checkbox" name="is_active" checked>
                            <span class="switch-track"></span>
                            <span>Aktifkan Produk</span>
                        </label>
                        <label class="form-switch">
                            <input type="checkbox" name="show_on_landing" checked>
                            <span class="switch-track"></span>
                            <span>Tampilkan di Landing Page</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Produk</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewPhoto(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('photoPreview').style.display = 'block';
            document.getElementById('previewImg').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
}
function removePhoto() {
    document.getElementById('photoInput').value = '';
    document.getElementById('photoPreview').style.display = 'none';
}
</script>
@endsection
