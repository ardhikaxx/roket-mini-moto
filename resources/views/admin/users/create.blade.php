@extends('layouts.admin')
@section('title', 'Tambah Pengguna')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.users.index') }}">Pengguna</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Tambah Pengguna</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Tambah Pengguna</h1></div></div></div>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-3" style="font-size:15px;">Informasi Akun</h5>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Nama Lengkap <span class="required">*</span></label><input type="text" name="name" class="form-control" required value="{{ old('name') }}"></div>
                        <div class="form-group"><label class="form-label">Username <span class="required">*</span></label><input type="text" name="username" class="form-control" required value="{{ old('username') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">PIN 4 Digit <span class="required">*</span></label><input type="password" name="pin" class="form-control" required maxlength="4" pattern="[0-9]{4}"></div>
                        <div class="form-group"><label class="form-label">No. Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea></div>

                    <h5 class="fw-bold mb-3 mt-4" style="font-size:15px;">Role & Akses</h5>
                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <select name="role" id="roleSelect" class="form-select" onchange="toggleStores()">
                            <option value="karyawan" {{ old('role')=='karyawan'?'selected':'' }}>Karyawan</option>
                            <option value="kepala_toko" {{ old('role')=='kepala_toko'?'selected':'' }}>Kepala Toko</option>
                            <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin / Owner</option>
                        </select>
                    </div>

                    <div id="storeSelection" class="form-group" style="display:{{ old('role')=='admin' ? 'none' : 'block' }};">
                        <label class="form-label">Penugasan Toko / Cabang</label>
                        <div class="row mt-2">
                            @foreach($stores as $s)
                            <div class="col-md-6 mb-2">
                                <label class="form-check" onclick="this.classList.toggle('checked');this.querySelector('input').checked=!this.querySelector('input').checked;">
                                    <input type="checkbox" name="store_ids[]" value="{{ $s->id }}" style="display:none;" {{ collect(old('store_ids'))->contains($s->id) ? 'checked' : '' }}>
                                    <span class="check-indicator"><i class="fa-solid fa-check" style="font-size:10px;"></i></span>
                                    <div><div style="font-weight:600;font-size:14px;">{{ $s->name }}</div><div style="font-size:11px;color:var(--text-secondary);">{{ $s->address }}</div></div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group"><label class="form-label">Foto Profil</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
                    <div class="form-group mt-4">
                        <label class="form-switch"><input type="checkbox" name="is_active" checked><span class="switch-track"></span><span>Aktifkan Akun</span></label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Pengguna</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
<script>
function toggleStores() {
    const role = document.getElementById('roleSelect').value;
    const div = document.getElementById('storeSelection');
    if (role === 'admin') { div.style.display = 'none'; } else { div.style.display = 'block'; }
}
</script>
@endsection
