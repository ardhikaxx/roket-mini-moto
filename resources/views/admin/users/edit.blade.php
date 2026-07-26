@extends('layouts.admin')
@section('title', 'Edit Pengguna: ' . $user->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.users.index') }}">Manajemen Pengguna</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Edit Akses</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-user-pen text-primary me-2"></i>Edit Data Pengguna</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;">Ubah profil, hak akses, dan penugasan toko untuk <strong>{{ $user->name }}</strong></p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-light px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-arrow-left me-2"></i> Batal / Kembali</a>
        </div>
    </div>
</div>

<form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row g-4 mb-5">
        {{-- Kolom Kiri: Informasi Utama --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
                <div class="card-header bg-white p-4 border-bottom border-light">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-address-card text-primary me-2"></i> Data Profil Pengguna
                    </h5>
                </div>
                <div class="card-body p-4 bg-neutral-50">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-id-card"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 ps-0" required value="{{ old('name', $user->name) }}" placeholder="Contoh: Budi Santoso">
                                </div>
                                @error('name')<small class="text-danger mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Username Akses <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-at"></i></span>
                                    <input type="text" name="username" class="form-control border-start-0 ps-0 font-monospace" required value="{{ old('username', $user->username) }}">
                                </div>
                                @error('username')<small class="text-danger mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-0">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Nomor Telepon / WA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-brands fa-whatsapp"></i></span>
                                    <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone', $user->phone) }}" placeholder="08123456789">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label fw-bold" style="font-size:13px;">Alamat Domisili</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-location-dot"></i></span>
                                    <input type="text" name="address" class="form-control border-start-0 ps-0" value="{{ old('address', $user->address) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
                <div class="card-header bg-white p-4 border-bottom border-light">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-user-shield text-primary me-2"></i> Kewenangan & Penugasan
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="form-group mb-4">
                        <label class="form-label fw-bold mb-3" style="font-size:13px;">Peran & Level Akses <span class="text-danger">*</span></label>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="w-100 m-0 cursor-pointer" onclick="updateRoleUI()">
                                    <input type="radio" name="role" value="karyawan" id="role_karyawan" class="d-none" {{ old('role', $user->role) == 'karyawan' ? 'checked' : '' }}>
                                    <div class="role-selector p-3 text-center border rounded-3 position-relative transition-all" id="card_karyawan" style="background:var(--neutral-50);">
                                        <div class="position-absolute d-none check-mark" style="top:-8px;right:-8px;width:24px;height:24px;background:var(--primary);color:white;border-radius:50%;align-items:center;justify-content:center;"><i class="fa-solid fa-check" style="font-size:12px;"></i></div>
                                        <i class="fa-solid fa-user-tie mb-2" style="font-size:24px;color:var(--text-secondary);"></i>
                                        <div class="fw-bold text-dark" style="font-size:14px;">Karyawan</div>
                                        <div class="text-muted mt-1" style="font-size:11px;">Input laporan harian</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="w-100 m-0 cursor-pointer" onclick="updateRoleUI()">
                                    <input type="radio" name="role" value="kepala_toko" id="role_kepala_toko" class="d-none" {{ old('role', $user->role) == 'kepala_toko' ? 'checked' : '' }}>
                                    <div class="role-selector p-3 text-center border rounded-3 position-relative transition-all" id="card_kepala_toko" style="background:var(--neutral-50);">
                                        <div class="position-absolute d-none check-mark" style="top:-8px;right:-8px;width:24px;height:24px;background:var(--primary);color:white;border-radius:50%;align-items:center;justify-content:center;"><i class="fa-solid fa-check" style="font-size:12px;"></i></div>
                                        <i class="fa-solid fa-user-gear mb-2" style="font-size:24px;color:var(--text-secondary);"></i>
                                        <div class="fw-bold text-dark" style="font-size:14px;">Kepala Toko</div>
                                        <div class="text-muted mt-1" style="font-size:11px;">Validasi level cabang</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="w-100 m-0 cursor-pointer" onclick="updateRoleUI()">
                                    <input type="radio" name="role" value="admin" id="role_admin" class="d-none" {{ old('role', $user->role) == 'admin' ? 'checked' : '' }}>
                                    <div class="role-selector p-3 text-center border rounded-3 position-relative transition-all" id="card_admin" style="background:var(--neutral-50);">
                                        <div class="position-absolute d-none check-mark" style="top:-8px;right:-8px;width:24px;height:24px;background:var(--primary);color:white;border-radius:50%;align-items:center;justify-content:center;"><i class="fa-solid fa-check" style="font-size:12px;"></i></div>
                                        <i class="fa-solid fa-user-shield mb-2" style="font-size:24px;color:var(--text-secondary);"></i>
                                        <div class="fw-bold text-dark" style="font-size:14px;">Administrator</div>
                                        <div class="text-muted mt-1" style="font-size:11px;">Akses seluruh sistem</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="storeSelection" class="form-group mb-0 p-3 rounded-3 border border-primary-100 bg-primary-50" style="display:{{ old('role', $user->role) == 'admin' ? 'none' : 'block' }};">
                        <label class="form-label fw-bold mb-3 text-primary-700" style="font-size:13px;">Penugasan Toko (Pilih satu atau lebih)</label>
                        <div class="row g-2">
                            @foreach($stores as $s)
                            <div class="col-md-6">
                                <label class="form-check m-0 p-0 position-relative cursor-pointer store-check-label" style="display:block;">
                                    <input type="checkbox" name="store_ids[]" value="{{ $s->id }}" class="store-checkbox d-none" {{ $user->stores->contains('id', $s->id) ? 'checked' : '' }} onchange="updateStoreUI(this)">
                                    <div class="store-card border bg-white p-2 rounded d-flex align-items-center gap-2 transition-all">
                                        <div class="store-check-indicator flex-shrink-0" style="width:20px;height:20px;border-radius:4px;border:2px solid var(--neutral-300);display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
                                            <i class="fa-solid fa-check text-white" style="font-size:10px; opacity:0;"></i>
                                        </div>
                                        <div style="min-width:0;">
                                            <div class="fw-bold text-dark text-truncate" style="font-size:13px;">{{ $s->name }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="adminNotice" class="p-3 rounded-3 border border-danger-100 bg-danger-50 text-center" style="display:{{ old('role', $user->role) == 'admin' ? 'block' : 'none' }};">
                        <i class="fa-solid fa-triangle-exclamation text-danger mb-2" style="font-size:24px;"></i>
                        <div class="fw-bold text-danger-700 mb-1" style="font-size:14px;">Akses Penuh Tanpa Batas</div>
                        <div class="text-danger" style="font-size:12px;">Akun Administrator tidak memerlukan penugasan toko karena dapat mengakses dan mengubah seluruh data di semua cabang.</div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Media & Konfigurasi --}}
        <div class="col-12 col-lg-4">
            
            <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
                <div class="card-header bg-white p-4 border-bottom border-light">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-image-portrait text-primary me-2"></i> Foto Profil Pengguna
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div id="imagePreview" style="width:140px; height:140px; margin:0 auto; border-radius:50%; background:var(--neutral-100); border:2px dashed var(--neutral-300); display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative;">
                            <img id="previewImg" src="{{ $user->photo ? asset('storage/'.$user->photo) : '' }}" style="width:100%; height:100%; object-fit:cover; display:{{ $user->photo ? 'block' : 'none' }}; position:absolute; top:0; left:0;">
                            
                            <div class="text-muted text-center" id="imagePlaceholder" style="display:{{ $user->photo ? 'none' : 'block' }};">
                                <i class="fa-solid fa-camera mb-1" style="font-size:28px;"></i>
                                <div style="font-size:11px;">Opsional</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-start">
                        <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewUserImage(this)">
                        <small class="text-muted mt-2 d-block text-center" style="font-size:11px;">Abaikan jika tidak ingin mengubah foto profil.</small>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
                <div class="card-header bg-white p-4 border-bottom border-light">
                    <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-power-off text-primary me-2"></i> Status Akun
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="p-3 rounded {{ $user->is_active ? 'bg-success-50 border-success-100' : 'bg-danger-50 border-danger-100' }} border mb-4" id="statusContainer">
                        <label class="form-switch d-flex align-items-center gap-3 m-0 cursor-pointer">
                            <input type="checkbox" name="is_active" {{ $user->is_active ? 'checked' : '' }} style="width:40px; height:24px;" onchange="toggleStatusStyle(this)">
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size:14px;" id="statusTitle">Aktifkan Akses Login</span>
                                <small class="text-muted" style="font-size:11px;" id="statusDesc">Pengguna bisa langsung login.</small>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="font-size:15px; border-radius:12px; box-shadow:0 8px 16px rgba(230,57,70,0.2);">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
/* Custom Styles for Interactive Role & Store Selection */
.role-selector { cursor: pointer; border-color: var(--border-light) !important; }
.role-selector:hover { border-color: var(--primary) !important; background: white !important; }
input[type="radio"]:checked + .role-selector { border-color: var(--primary) !important; background: var(--primary-50) !important; box-shadow: 0 4px 12px rgba(230,57,70,0.1); }
input[type="radio"]:checked + .role-selector i { color: var(--primary) !important; }
input[type="radio"]:checked + .role-selector .check-mark { display: flex !important; }

.store-card { border-color: var(--border-light) !important; }
.store-check-label:hover .store-card { border-color: var(--primary) !important; background: var(--primary-50) !important; }
input.store-checkbox:checked + .store-card { border-color: var(--primary) !important; background: var(--primary-50) !important; }
input.store-checkbox:checked + .store-card .store-check-indicator { background: var(--primary); border-color: var(--primary) !important; }
input.store-checkbox:checked + .store-card .store-check-indicator i { opacity: 1 !important; }
</style>

<script>
function previewUserImage(input) {
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
    }
}

function updateRoleUI() {
    setTimeout(() => {
        let selectedRole = document.querySelector('input[name="role"]:checked');
        if(!selectedRole) return;
        
        let role = selectedRole.value;
        const storeDiv = document.getElementById('storeSelection');
        const adminDiv = document.getElementById('adminNotice');
        
        if (role === 'admin') { 
            storeDiv.style.display = 'none'; 
            adminDiv.style.display = 'block';
        } else { 
            storeDiv.style.display = 'block'; 
            adminDiv.style.display = 'none';
        }
    }, 10);
}

function toggleStatusStyle(checkbox) {
    const container = document.getElementById('statusContainer');
    const title = document.getElementById('statusTitle');
    const desc = document.getElementById('statusDesc');
    
    if (checkbox.checked) {
        container.className = 'p-3 rounded border mb-4 bg-success-50 border-success-100';
        title.className = 'fw-bold d-block text-success-700';
        desc.className = 'text-success';
        desc.innerText = 'Pengguna bisa langsung login.';
    } else {
        container.className = 'p-3 rounded border mb-4 bg-danger-50 border-danger-100';
        title.className = 'fw-bold d-block text-danger-700';
        desc.className = 'text-danger';
        desc.innerText = 'Akun diblokir sementara.';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateRoleUI();
    const checkbox = document.querySelector('input[name="is_active"]');
    if(checkbox) toggleStatusStyle(checkbox);
});
</script>
@endsection
