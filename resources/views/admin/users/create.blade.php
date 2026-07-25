@extends('layouts.admin')
@section('title', 'Tambah Pengguna')
@section('content')
<h2 class="fw-bold mb-4">Tambah Pengguna</h2>
<div class="card shadow-sm p-4 border-0">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>PIN 4 Digit <span class="text-danger">*</span></label>
                <input type="password" name="pin" class="form-control" required maxlength="4" pattern="[0-9]{4}">
            </div>
            <div class="col-md-6 mb-3">
                <label>No. Telepon</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Role <span class="text-danger">*</span></label>
                <select name="role" id="roleSelect" class="form-select" required onchange="toggleStore()">
                    <option value="karyawan">Karyawan</option>
                    <option value="kepala_toko">Kepala Toko</option>
                    <option value="admin">Admin / Owner</option>
                </select>
            </div>
            
            <div class="col-md-12 mb-3" id="storeSelection">
                <label>Penugasan Toko / Cabang <span class="text-danger">*</span></label>
                <div class="row mt-2">
                    @foreach($stores as $s)
                    <div class="col-md-4">
                        <div class="form-check">
                          <input class="form-check-input store-checkbox" type="checkbox" name="store_ids[]" value="{{ $s->id }}" id="store_{{ $s->id }}">
                          <label class="form-check-label" for="store_{{ $s->id }}">{{ $s->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked value="1">
                  <label class="form-check-label" for="is_active">Aktifkan Akun</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Simpan Pengguna</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
</div>
<script>
function toggleStore() {
    var role = document.getElementById('roleSelect').value;
    var storeDiv = document.getElementById('storeSelection');
    if(role === 'admin') {
        storeDiv.style.display = 'none';
        document.querySelectorAll('.store-checkbox').forEach(cb => cb.checked = false);
    } else {
        storeDiv.style.display = 'block';
    }
}
document.addEventListener('DOMContentLoaded', toggleStore);
</script>
@endsection