@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('content')
<h2 class="fw-bold mb-4">Tambah Produk Baru</h2>
<div class="card shadow-sm p-4 border-0">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>SKU / Kode <span class="text-danger">*</span></label>
                <input type="text" name="sku" class="form-control" required value="{{ old('sku') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $c) <option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option> @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Harga Jual (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="price" class="form-control" required min="0" value="{{ old('price') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Harga Modal (Rp)</label>
                <input type="number" name="cost_price" class="form-control" min="0" value="{{ old('cost_price') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Stok Awal <span class="text-danger">*</span></label>
                <input type="number" name="stock" class="form-control" required min="0" value="{{ old('stock', 0) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Satuan (Unit) <span class="text-danger">*</span></label>
                <input type="text" name="unit" class="form-control" required value="{{ old('unit', 'pcs') }}">
            </div>
            <div class="col-md-12 mb-3">
                <label>Foto Produk (Max 2MB)</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
            <div class="col-md-12 mb-3">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked value="1">
                  <label class="form-check-label" for="is_active">Aktifkan Produk</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="show_on_landing" id="show_on_landing" checked value="1">
                  <label class="form-check-label" for="show_on_landing">Tampilkan di Landing Page</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Simpan Produk</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
</div>
@endsection