@extends('layouts.admin')
@section('title', 'Tambah Toko')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stores.index') }}">Toko</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Tambah Toko</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Tambah Toko Baru</h1><p class="page-subtitle">Tambahkan cabang toko baru</p></div></div></div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.stores.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Kode Toko <span class="required">*</span></label><input type="text" name="code" class="form-control" required value="{{ old('code') }}"><div class="form-hint">Contoh: BDW-01, SIT-01</div></div>
                        <div class="form-group"><label class="form-label">Nama Toko <span class="required">*</span></label><input type="text" name="name" class="form-control" required value="{{ old('name') }}"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Alamat Lengkap <span class="required">*</span></label><textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">No. Telepon / WA</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
                        <div class="form-group"><label class="form-label">Jam Operasional</label><input type="text" name="operational_hours" class="form-control" value="{{ old('operational_hours') }}" placeholder="Senin-Sabtu: 08:00-21:00"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Koordinat (optional)</label><input type="text" name="coordinates" class="form-control" value="{{ old('coordinates') }}" placeholder="-7.9105, 113.8237"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Keterangan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group"><label class="form-label">Foto Toko</label>
                        <input type="file" name="photo" class="form-control" accept="image/*"></div>
                    <div class="form-group mt-4">
                        <label class="form-switch"><input type="checkbox" name="is_active" checked><span class="switch-track"></span><span>Aktifkan Toko</span></label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Toko</button>
                <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
