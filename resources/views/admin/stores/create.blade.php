@extends('layouts.admin')
@section('title', 'Tambah Toko')
@section('content')
<h2 class="fw-bold mb-4">Tambah Toko Baru</h2>
<div class="card shadow-sm p-4 border-0">
    <form action="{{ route('admin.stores.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Kode Toko / Cabang <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" required value="{{ old('code') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Nama Toko <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="col-md-12 mb-3">
                <label>No. Telepon / WA</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-12 mb-3">
                <label>Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
            </div>
            <div class="col-md-12 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked value="1">
                  <label class="form-check-label" for="is_active">Aktifkan Toko</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Simpan Toko</button>
        <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
</div>
@endsection