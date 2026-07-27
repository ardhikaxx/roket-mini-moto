@extends('layouts.admin')
@section('title', 'Profile')
@section('content')
@php $user = auth()->user(); @endphp

<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-regular fa-circle-user text-primary me-2"></i>Profile Saya</h1>
            <p class="page-subtitle">Kelola informasi akun dan keamanan Anda</p>
        </div>
        <div class="page-actions">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill" style="font-size:13px;font-weight:600;">
                <i class="fa-regular fa-calendar me-1"></i> Bergabung {{ $user->created_at->format('d M Y') }}
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left Column - Profile Card --}}
    <div class="col-lg-4">
        <div class="card border-0 h-100" style="border-radius:var(--radius-xl); overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.04);">
            {{-- Cover --}}
            <div style="height:120px; background:linear-gradient(135deg, var(--primary), var(--primary-700)); position:relative;">
                <div style="position:absolute;inset:0;background:url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\"); opacity:0.4;"></div>
            </div>

            {{-- Avatar --}}
            <div class="text-center" style="margin-top:-48px;">
                <div id="profileAvatarPreview" style="width:96px;height:96px;border-radius:50%;border:4px solid #fff;background:var(--primary-100);display:inline-flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:var(--primary-700);overflow:hidden;position:relative;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    @if($user->photo)
                        <img src="{{ asset('storage/'.$user->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    @endif
                </div>
            </div>

            <div class="card-body text-center pt-3 px-4 pb-4">
                <h4 class="fw-bold mb-1" style="font-size:20px;">{{ $user->name }}</h4>
                <div class="text-muted mb-2" style="font-size:13px;"><i class="fa-regular fa-at me-1"></i>{{ $user->username }}</div>

                @php
                    $roleClass = $user->isAdmin() ? 'badge-danger' : ($user->isKepalaToko() ? 'badge-warning' : 'badge-info');
                    $roleIcon = $user->isAdmin() ? 'fa-shield-halved' : ($user->isKepalaToko() ? 'fa-store' : 'fa-user-tie');
                @endphp
                <span class="badge {{ $roleClass }} rounded-pill px-3 mb-3" style="font-size:12px;font-weight:600;padding-top:6px;padding-bottom:6px;">
                    <i class="fa-solid {{ $roleIcon }} me-1"></i> {{ str_replace('_', ' ', ucfirst($user->role)) }}
                </span>

                <div class="d-flex justify-content-center gap-3 mt-2 pt-3" style="border-top:1px solid var(--border-light);">
                    <div class="text-center">
                        <div class="fw-bold" style="font-size:18px;color:var(--text);">{{ $user->phone ?? '-' }}</div>
                        <div class="text-muted" style="font-size:11px;">Telepon</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold" style="font-size:18px;color:var(--text);">{{ $user->stores->count() }}</div>
                        <div class="text-muted" style="font-size:11px;">Toko</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-8">
        {{-- Edit Profil --}}
        <div class="card border-0 mb-4" style="border-radius:var(--radius-xl); box-shadow:0 4px 20px rgba(0,0,0,0.04);">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-3 px-4 py-3">
                <div style="width:40px;height:40px;border-radius:12px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;">
                    <i class="fa-regular fa-pen-to-square"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="font-size:16px;">Edit Profil</h5>
                    <div class="text-muted" style="font-size:12px;">Perbarui informasi data diri Anda</div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label mb-1"><i class="fa-regular fa-user me-1 text-primary"></i> Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1"><i class="fa-regular fa-circle-user me-1 text-primary"></i> Username</label>
                            <input type="text" class="form-control" value="{{ $user->username }}" disabled style="background:var(--neutral-50);color:var(--text-secondary);">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1"><i class="fa-solid fa-phone me-1 text-primary"></i> No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 0812xxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1"><i class="fa-solid fa-location-dot me-1 text-primary"></i> Alamat</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}" placeholder="Alamat lengkap">
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1"><i class="fa-solid fa-camera me-1 text-primary"></i> Foto Profil</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="file" name="photo" id="photoInput" class="form-control" accept="image/*" style="max-width:300px;">
                                <small class="text-muted">Max 2MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pt-2" style="border-top:1px solid var(--border-light);">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="font-weight:600;">
                            <i class="fa-regular fa-floppy-disk me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ganti PIN --}}
        <div class="card border-0" style="border-radius:var(--radius-xl); box-shadow:0 4px 20px rgba(0,0,0,0.04);">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-3 px-4 py-3">
                <div style="width:40px;height:40px;border-radius:12px;background:var(--warning-50);color:var(--warning);display:flex;align-items:center;justify-content:center;font-size:18px;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="font-size:16px;">Ganti PIN</h5>
                    <div class="text-muted" style="font-size:12px;">Perbarui PIN keamanan akun Anda</div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.change-pin') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label mb-1"><i class="fa-solid fa-key me-1 text-warning"></i> PIN Saat Ini</label>
                            <div class="input-group">
                                <input type="password" name="current_pin" class="form-control" required maxlength="4" pattern="[0-9]{4}" inputmode="numeric" autocomplete="off" style="letter-spacing:6px;font-size:18px;font-weight:700;">
                                <button class="btn btn-outline-secondary toggle-pin" type="button" style="border-color:var(--border);" onclick="togglePin(this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1"><i class="fa-solid fa-lock me-1 text-warning"></i> PIN Baru</label>
                            <div class="input-group">
                                <input type="password" name="new_pin" class="form-control" required maxlength="4" pattern="[0-9]{4}" inputmode="numeric" autocomplete="off" style="letter-spacing:6px;font-size:18px;font-weight:700;">
                                <button class="btn btn-outline-secondary toggle-pin" type="button" style="border-color:var(--border);" onclick="togglePin(this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1"><i class="fa-solid fa-check-double me-1 text-warning"></i> Konfirmasi PIN</label>
                            <div class="input-group">
                                <input type="password" name="new_pin_confirmation" class="form-control" required maxlength="4" pattern="[0-9]{4}" inputmode="numeric" autocomplete="off" style="letter-spacing:6px;font-size:18px;font-weight:700;">
                                <button class="btn btn-outline-secondary toggle-pin" type="button" style="border-color:var(--border);" onclick="togglePin(this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pt-2" style="border-top:1px solid var(--border-light);">
                        <button type="submit" class="btn btn-warning px-4 py-2" style="font-weight:600;">
                            <i class="fa-solid fa-key me-2"></i> Ganti PIN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle PIN visibility
function togglePin(btn) {
    const input = btn.closest('.input-group').querySelector('input');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Live photo preview
document.getElementById('photoInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        const preview = document.getElementById('profileAvatarPreview');
        preview.innerHTML = '<img src="'+ev.target.result+'" style="width:100%;height:100%;object-fit:cover;">';
    };
    reader.readAsDataURL(file);
});

// Auto-submit PIN forms on enter
document.querySelectorAll('input[maxlength="4"]').forEach(el => {
    el.addEventListener('keyup', function(e) {
        if (this.value.length >= 4 && e.key !== 'Backspace') {
            const inputs = this.closest('.row').querySelectorAll('input[maxlength="4"]');
            const idx = Array.from(inputs).indexOf(this);
            if (idx < inputs.length - 1) inputs[idx + 1].focus();
        }
    });
});
</script>
@endpush
