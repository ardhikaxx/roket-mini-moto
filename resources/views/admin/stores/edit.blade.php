@extends('layouts.admin')
@section('title', 'Edit '.$store->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stores.index') }}">Toko</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $store->name }}</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Edit Toko: {{ $store->name }}</h1></div></div></div>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.stores.update', $store->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Kode Toko <span class="required">*</span></label><input type="text" name="code" class="form-control" required value="{{ old('code', $store->code) }}"></div>
                        <div class="form-group"><label class="form-label">Nama Toko <span class="required">*</span></label><input type="text" name="name" class="form-control" required value="{{ old('name', $store->name) }}"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Alamat <span class="required">*</span></label><textarea name="address" class="form-control" rows="3" required>{{ old('address', $store->address) }}</textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $store->phone) }}"></div>
                        <div class="form-group"><label class="form-label">Jam Operasional</label><input type="text" name="operational_hours" class="form-control" value="{{ old('operational_hours', $store->operational_hours) }}"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Koordinat</label><input type="text" name="coordinates" class="form-control" value="{{ old('coordinates', $store->coordinates) }}"></div>
                    <div class="form-group"><label class="form-label">Keterangan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $store->notes) }}</textarea></div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group"><label class="form-label">Ganti Foto</label><input type="file" name="photo" class="form-control" accept="image/*">
                        @if($store->photo)<div class="form-hint">Foto saat ini: <a href="{{ asset('storage/'.$store->photo) }}" target="_blank">Lihat</a></div>@endif
                    </div>
                    <div class="form-group mt-4">
                        <label class="form-switch"><input type="checkbox" name="is_active" {{ $store->is_active ? 'checked' : '' }}><span class="switch-track"></span><span>Toko Aktif</span></label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
