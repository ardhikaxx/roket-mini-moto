@extends('layouts.admin')
@section('title', 'Edit '.$product->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.products.index') }}">Produk</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $product->name }}</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Edit Produk</h1><p class="page-subtitle">{{ $product->name }}</p></div></div></div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-3" style="font-size:15px;">Informasi Produk</h5>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Nama Produk <span class="required">*</span></label><input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}"></div>
                        <div class="form-group"><label class="form-label">SKU <span class="required">*</span></label><input type="text" name="sku" class="form-control" required value="{{ old('sku', $product->sku) }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Kategori <span class="required">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $c) <option value="{{ $c->id }}" {{ old('category_id',$product->category_id)==$c->id?'selected':'' }}>{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Satuan <span class="required">*</span></label><input type="text" name="unit" class="form-control" required value="{{ old('unit', $product->unit) }}"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea></div>

                    <h5 class="fw-bold mb-3 mt-4" style="font-size:15px;">Harga & Stok</h5>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Harga Jual (Rp) <span class="required">*</span></label><input type="number" name="price" class="form-control" required min="0" value="{{ old('price', $product->price) }}"></div>
                        <div class="form-group"><label class="form-label">Harga Modal (Rp)</label><input type="number" name="cost_price" class="form-control" min="0" value="{{ old('cost_price', $product->cost_price) }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Stok <span class="required">*</span></label><input type="number" name="stock" class="form-control" required min="0" value="{{ old('stock', $product->stock) }}"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3" style="font-size:15px;">Media Produk</h5>
                    <div class="form-group">
                        <label class="form-label">Foto Produk</label>
                        @if($product->photo)
                            <div style="margin-bottom:10px;border-radius:var(--radius-md);overflow:hidden;">
                                <img src="{{ asset('storage/'.$product->photo) }}" style="width:100%;height:160px;object-fit:cover;">
                            </div>
                        @endif
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <div class="form-hint">Kosongi jika tidak ingin mengubah foto</div>
                    </div>

                    <h5 class="fw-bold mb-3 mt-4" style="font-size:15px;">Status & Publikasi</h5>
                    <div class="form-group">
                        <label class="form-switch mb-3">
                            <input type="checkbox" name="is_active" {{ $product->is_active ? 'checked' : '' }}>
                            <span class="switch-track"></span>
                            <span>Produk Aktif</span>
                        </label>
                        <label class="form-switch">
                            <input type="checkbox" name="show_on_landing" {{ $product->show_on_landing ? 'checked' : '' }}>
                            <span class="switch-track"></span>
                            <span>Tampilkan di Landing Page</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
