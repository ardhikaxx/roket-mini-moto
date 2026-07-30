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

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Card Informasi Produk --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                        <span style="width:32px;height:32px;border-radius:8px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;margin-right:12px;"><i class="fa-solid fa-circle-info" style="font-size:14px;"></i></span>
                        Informasi Dasar
                    </h5>
                    
                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" required value="{{ old('name') }}" placeholder="Contoh: Helm Bogo Retro Classy">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @else <div class="form-text">Nama produk akan ditampilkan kepada kasir dan di struk.</div> @enderror
                    </div>
                    
                    <div class="form-row mb-4">
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold">SKU / Kode Produk <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" required value="{{ old('sku') }}" placeholder="Contoh: HLM-BOGO-001">
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" required value="{{ old('unit', 'pcs') }}" placeholder="Contoh: pcs, set">
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Tuliskan deskripsi mengenai bahan, ukuran, atau fitur produk ini...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Card Harga & Stok --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                        <span style="width:32px;height:32px;border-radius:8px;background:var(--success-50);color:var(--success-600);display:flex;align-items:center;justify-content:center;margin-right:12px;"><i class="fa-solid fa-tags" style="font-size:14px;"></i></span>
                        Harga & Stok
                    </h5>
                    
                    <div class="form-row mb-4">
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="text" name="price" inputmode="numeric" class="form-control input-rupiah border-start-0 ps-0 @error('price') is-invalid @enderror" required value="{{ old('price') }}" placeholder="0">
                            </div>
                            @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold">Harga Modal (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="text" name="cost_price" inputmode="numeric" class="form-control input-rupiah border-start-0 ps-0 @error('cost_price') is-invalid @enderror" value="{{ old('cost_price') }}" placeholder="0">
                            </div>
                            <div class="form-text">Opsional. Digunakan untuk menghitung profit.</div>
                        </div>
                    </div>
                    
                    <div class="form-row mb-0">
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" required min="0" value="{{ old('stock', 0) }}">
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group mb-0 d-flex align-items-end">
                            <div class="p-3 bg-light rounded w-100 text-center border">
                                <span class="text-muted" style="font-size:13px;"><i class="fa-solid fa-circle-info me-1"></i> Stok ini akan dicatat untuk seluruh cabang utama.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Card Media Produk --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                        <span style="width:32px;height:32px;border-radius:8px;background:var(--warning-50);color:var(--warning-700);display:flex;align-items:center;justify-content:center;margin-right:12px;"><i class="fa-solid fa-image" style="font-size:14px;"></i></span>
                        Media Produk
                    </h5>
                    
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold">Foto Produk</label>
                        <div class="upload-zone" id="uploadZone" onclick="document.getElementById('photoInput').click()">
                            <div class="upload-zone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="upload-zone-text text-dark fw-bold mb-1">Pilih atau letakkan gambar</div>
                            <div class="upload-zone-text text-muted" style="font-size:12px;">Format JPG, PNG (Maks 2MB)</div>
                            <input type="file" id="photoInput" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(event)">
                        </div>
                        <div id="photoPreview" style="display:none;margin-top:16px;border-radius:12px;overflow:hidden;position:relative;border:1px solid var(--border-light);box-shadow:var(--shadow-sm);">
                            <img id="previewImg" style="width:100%;height:220px;object-fit:cover;">
                            <button type="button" class="btn btn-danger btn-sm" style="position:absolute;top:12px;right:12px;border-radius:50%;width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.2);" onclick="removePhoto()" title="Hapus Foto"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                        @error('photo') <div class="text-danger mt-2" style="font-size:13px;">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Card Status & Pengaturan --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-size:16px;">
                        <span style="width:32px;height:32px;border-radius:8px;background:var(--info-50);color:var(--info-700);display:flex;align-items:center;justify-content:center;margin-right:12px;"><i class="fa-solid fa-toggle-on" style="font-size:14px;"></i></span>
                        Pengaturan Tambahan
                    </h5>
                    
                    <div class="form-group mb-4 pb-4 border-bottom border-light">
                        <label class="form-switch w-100">
                            <input type="checkbox" name="is_active" checked value="1">
                            <span class="switch-track"></span>
                            <div style="margin-left:8px;">
                                <div class="fw-semibold text-dark">Aktifkan Produk</div>
                                <div class="text-muted" style="font-size:12px;margin-top:2px;">Produk dapat dijual oleh kasir</div>
                            </div>
                        </label>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="form-switch w-100">
                            <input type="checkbox" name="show_on_landing" checked value="1">
                            <span class="switch-track"></span>
                            <div style="margin-left:8px;">
                                <div class="fw-semibold text-dark">Tampilkan di Landing Page</div>
                                <div class="text-muted" style="font-size:12px;margin-top:2px;">Bisa dilihat oleh pelanggan publik</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex align-items-center justify-content-end gap-3 mt-2 mb-5">
        <a href="{{ route('admin.products.index') }}" class="btn btn-light px-4 py-2" style="font-weight:600;border:1px solid var(--border-light);">Batal</a>
        <button type="submit" class="btn btn-primary px-4 py-2" style="font-weight:600;box-shadow:0 4px 12px rgba(230, 57, 70, 0.25);"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Produk</button>
    </div>
</form>

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
