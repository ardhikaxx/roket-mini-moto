@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('content')
<h2 class="fw-bold mb-4">Edit Produk: {{ $product->name }}</h2>
<div class="card shadow-sm p-4 border-0">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>SKU / Kode <span class="text-danger">*</span></label>
                <input type="text" name="sku" class="form-control" required value="{{ old('sku', $product->sku) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $c) <option value="{{ $c->id }}" {{ old('category_id', $product->category_id)==$c->id?'selected':'' }}>{{ $c->name }}</option> @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Harga Jual (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="price" class="form-control" required min="0" value="{{ old('price', $product->price) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Harga Modal (Rp)</label>
                <input type="number" name="cost_price" class="form-control" min="0" value="{{ old('cost_price', $product->cost_price) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Stok Tersedia <span class="text-danger">*</span></label>
                <input type="number" name="stock" class="form-control" required min="0" value="{{ old('stock', $product->stock) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Satuan (Unit) <span class="text-danger">*</span></label>
                <input type="text" name="unit" class="form-control" required value="{{ old('unit', $product->unit) }}">
            </div>
            <div class="col-md-12 mb-3">
                <label>Ganti Foto Produk (Max 2MB) - Kosongi jika tidak diubah</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                @if($product->photo) <img src="{{ asset('storage/'.$product->photo) }}" width="100" class="mt-2 rounded"> @endif
            </div>
            <div class="col-md-12 mb-3">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">Aktifkan Produk</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="show_on_landing" id="show_on_landing" value="1" {{ $product->show_on_landing ? 'checked' : '' }}>
                  <label class="form-check-label" for="show_on_landing">Tampilkan di Landing Page</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
</div>
@endsection