@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.products.index') }}">Produk</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Tambah Produk</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-box-open text-primary me-2"></i>Tambah Produk Baru</h1>
            <p class="page-subtitle">Lengkapi informasi produk untuk menambah ke katalog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-5">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                        <span style="width:28px;height:28px;border-radius:6px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;margin-right:10px;"><i class="fa-solid fa-circle-info" style="font-size:14px;"></i></span>
                        Informasi Produk
                    </h5>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Nama Produk <span class="required text-danger">*</span></label><input type="text" name="name" class="form-control @error('name') error @enderror" required value="{{ old('name') }}" placeholder="Contoh: Helm Bogo Retro"></div>
                        <div class="form-group"><label class="form-label">SKU / Kode Produk <span class="required text-danger">*</span></label><input type="text" name="sku" class="form-control @error('sku') error @enderror" required value="{{ old('sku') }}" placeholder="Contoh: HLM-BOGO-001"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Kategori <span class="required">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $c) <option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Satuan <span class="required text-danger">*</span></label><input type="text" name="unit" class="form-control" required value="{{ old('unit', 'pcs') }}" placeholder="Contoh: pcs, set"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="4" placeholder="Tuliskan deskripsi lengkap mengenai produk ini...">{{ old('description') }}</textarea></div>

                    <hr class="my-5 border-light">

                    <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                        <span style="width:28px;height:28px;border-radius:6px;background:var(--success-50);color:var(--success-600);display:flex;align-items:center;justify-content:center;margin-right:10px;"><i class="fa-solid fa-tags" style="font-size:14px;"></i></span>
                        Harga & Stok
                    </h5>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Harga Jual (Rp) <span class="required text-danger">*</span></label><input type="number" name="price" class="form-control" required min="0" value="{{ old('price') }}" placeholder="0"></div>
                        <div class="form-group"><label class="form-label">Harga Modal (Rp)</label><input type="number" name="cost_price" class="form-control" min="0" value="{{ old('cost_price') }}" placeholder="0"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Stok Awal <span class="required text-danger">*</span></label><input type="number" name="stock" class="form-control" required min="0" value="{{ old('stock', 0) }}"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="p-4 rounded-4" style="background:var(--neutral-50);border:1px solid var(--border-light);">
                        <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                            <span style="width:28px;height:28px;border-radius:6px;background:var(--warning-50);color:var(--warning-700);display:flex;align-items:center;justify-content:center;margin-right:10px;"><i class="fa-solid fa-image" style="font-size:14px;"></i></span>
                            Media Produk
                        </h5>
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

                    <div class="p-4 rounded-4 mt-4" style="background:var(--neutral-50);border:1px solid var(--border-light);">
                        <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                            <span style="width:28px;height:28px;border-radius:6px;background:var(--info-50);color:var(--info-700);display:flex;align-items:center;justify-content:center;margin-right:10px;"><i class="fa-solid fa-toggle-on" style="font-size:14px;"></i></span>
                            Status & Publikasi
                        </h5>
                        <div class="form-group mb-0">
                            <label class="form-switch mb-4 w-100">
                                <input type="checkbox" name="is_active" checked>
                                <span class="switch-track"></span>
                                <span>Aktifkan Produk</span>
                            </label>
                            <label class="form-switch w-100">
                                <input type="checkbox" name="show_on_landing" checked>
                                <span class="switch-track"></span>
                                <span>Tampilkan di Landing Page</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center justify-content-end gap-3 mt-5 pt-4" style="border-top:1px solid var(--border-light);">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light px-4" style="font-weight:600;">Batal</a>
                <button type="submit" class="btn btn-primary px-4" style="font-weight:600;box-shadow:0 4px 12px rgba(230, 57, 70, 0.25);"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Produk</button>
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
