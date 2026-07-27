@extends('layouts.admin')
@section('title', 'Tambah Toko Baru')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stores.index') }}">Jaringan Toko</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Tambah Baru</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-store-circle text-primary me-2"></i>Tambah Cabang Toko</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;">Registrasi cabang baru untuk ekspansi jaringan toko Anda</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.stores.index') }}" class="btn btn-light px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-arrow-left me-2"></i> Kembali</a>
        </div>
    </div>
</div>

<form action="{{ route('admin.stores.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row g-4 mb-5">
        {{-- Kolom Kiri: Informasi Utama --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
                <div class="card-header bg-white p-4 border-bottom border-light">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i> Informasi Utama Cabang
                    </h5>
                </div>
                <div class="card-body p-4 bg-neutral-50">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Kode Toko <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-hashtag"></i></span>
                                    <input type="text" name="code" class="form-control border-start-0 ps-0" required value="{{ old('code') }}" placeholder="Contoh: BDW-01" style="font-weight:600; text-transform:uppercase;">
                                </div>
                                @error('code')<small class="text-danger mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Nama Toko / Cabang <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-store"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 ps-0" required value="{{ old('name') }}" placeholder="Contoh: Roket Mini Moto Cabang Bondowoso">
                                </div>
                                @error('name')<small class="text-danger mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label fw-bold" style="font-size:13px;">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="3" required placeholder="Masukkan alamat lengkap toko beserta detail jalan dan patokan...">{{ old('address') }}</textarea>
                        @error('address')<small class="text-danger mt-1">{{ $message }}</small>@enderror
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Nomor Telepon / WA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-brands fa-whatsapp"></i></span>
                                    <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone') }}" placeholder="08123456789">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Jam Operasional</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-clock"></i></span>
                                    <input type="text" name="operational_hours" class="form-control border-start-0 ps-0" value="{{ old('operational_hours') }}" placeholder="08:00 - 21:00 (Senin - Sabtu)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label fw-bold" style="font-size:13px;">Keterangan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan internal mengenai toko ini...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Media & Konfigurasi --}}
        <div class="col-12 col-lg-4">
            
            <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
                <div class="card-header bg-white p-4 border-bottom border-light">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-camera text-primary me-2"></i> Foto Fisik Toko
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div id="imagePreview" style="width:100%; height:200px; border-radius:12px; background:var(--neutral-100); border:2px dashed var(--neutral-300); display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative;">
                            <div class="text-muted text-center" id="imagePlaceholder">
                                <i class="fa-solid fa-image mb-2" style="font-size:32px;"></i>
                                <div style="font-size:13px;">Pratinjau Foto Toko</div>
                            </div>
                            <img id="previewImg" style="width:100%; height:100%; object-fit:cover; display:none; position:absolute; top:0; left:0;">
                        </div>
                    </div>
                    <div class="text-start">
                        <label class="form-label fw-bold" style="font-size:13px;">Upload Foto</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewImage(this)">
                        <small class="text-muted mt-2 d-block" style="font-size:11px;">Format disarankan: JPG/PNG. Maks 2MB.</small>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
                <div class="card-header bg-white p-4 border-bottom border-light">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-gear text-primary me-2"></i> Konfigurasi Operasional
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="p-3 rounded bg-white border border-light mb-4 shadow-sm">
                        <label class="form-label fw-bold d-block mb-2" style="font-size:13px;">Status Visibilitas</label>
                        <label class="form-switch d-flex align-items-center gap-3 m-0 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span class="switch-track" style="flex-shrink: 0;"></span>
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size:14px;">Aktifkan Cabang Ini</span>
                                <small class="text-muted" style="font-size:11px;">Jika aktif, toko ini bisa dipilih dalam pembuatan laporan harian.</small>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="font-size:15px; border-radius:12px; box-shadow:0 8px 16px rgba(230,57,70,0.2);">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Data Toko Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    const preview = document.getElementById('previewImg');
    const placeholder = document.getElementById('imagePlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
}
</script>
@endsection
