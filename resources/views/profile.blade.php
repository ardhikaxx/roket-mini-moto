@extends('layouts.admin')
@section('title', 'Profile')
@section('breadcrumb')
    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : (auth()->user()->isKepalaToko() ? 'kepalatoko.dashboard' : 'karyawan.dashboard')) }}">Dashboard</a>
    <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Profile</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Profile Saya</h1></div></div></div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3" style="font-size:32px;width:96px;height:96px;">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/'.auth()->user()->photo) }}">
                    @else
                        {{ strtoupper(substr(auth()->user()->name,0,2)) }}
                    @endif
                </div>
                <h4 class="fw-bold">{{ auth()->user()->name }}</h4>
                <div style="color:var(--text-secondary);font-size:13px;">@ {{ auth()->user()->username }}</div>
                <span class="badge badge-primary mt-2">{{ str_replace('_',' ',ucfirst(auth()->user()->role)) }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><h5 class="fw-bold mb-0">Edit Profil</h5></div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="name" class="form-control" required value="{{ old('name', auth()->user()->name) }}"></div>
                        <div class="form-group"><label class="form-label">No. Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ old('address', auth()->user()->address) }}</textarea></div>
                    <div class="form-group"><label class="form-label">Foto Profil</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
                    <button type="submit" class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="fw-bold mb-0">Ganti PIN</h5></div>
            <div class="card-body">
                <form action="{{ route('profile.change-pin') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">PIN Saat Ini</label><input type="password" name="current_pin" class="form-control" required maxlength="4" pattern="[0-9]{4}"></div>
                        <div class="form-group"><label class="form-label">PIN Baru (4 digit)</label><input type="password" name="new_pin" class="form-control" required maxlength="4" pattern="[0-9]{4}"></div>
                        <div class="form-group"><label class="form-label">Konfirmasi PIN Baru</label><input type="password" name="new_pin_confirmation" class="form-control" required maxlength="4" pattern="[0-9]{4}"></div>
                    </div>
                    <button type="submit" class="btn btn-warning mt-2"><i class="fa-solid fa-key"></i> Ganti PIN</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
