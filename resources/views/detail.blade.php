@extends('layouts.app')

@section('title', $product->name . ' - Roket Mini Moto')

@section('meta_description', Str::limit($product->description ?? 'Detail produk ' . $product->name . ' di Roket Mini Moto.', 160))


@section('content')
    <!-- Product Detail Section -->
    <section class="section-padding" style="padding-top: 100px;">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}#produk" class="text-decoration-none text-muted">Produk</a></li>
                    <li class="breadcrumb-item active fw-bold" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>

            <div class="row gx-5">
                <!-- Product Image -->
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        @php
                            $imgField = $product->image ?? $product->photo ?? null;
                            $imgPath = $imgField ? asset('storage/'.$imgField) : asset('assets/images/no-image.png');
                        @endphp
                        <img src="{{ $imgPath }}" class="w-100 object-fit-cover" style="height: 500px;" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/images/no-image.png') }}';">
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $product->category->name ?? 'Kategori Umum' }}</span>
                    </div>
                    <h2 class="fw-bold mb-3">{{ $product->name }}</h2>
                    <h3 class="text-primary-custom fw-bold mb-4">Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
                    
                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        {!! nl2br(e($product->description ?? 'Deskripsi produk belum tersedia.')) !!}
                    </p>

                    <!-- Spesifikasi -->
                    <div class="mb-5">
                        <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-list-check me-2 text-primary-custom"></i>Informasi Produk</h5>
                        <div class="row mb-2">
                            <div class="col-5 col-md-4 text-muted">Status Stok</div>
                            <div class="col-7 col-md-8 fw-bold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $product->stock > 0 ? 'Tersedia (' . $product->stock . ' Unit)' : 'Stok Habis' }}
                            </div>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="d-flex flex-wrap gap-3 mb-5">
                        <div class="d-flex align-items-center bg-white border rounded px-3 py-2">
                            <i class="fa-solid fa-shield-halved text-success me-2"></i> Garansi Toko
                        </div>
                        <div class="d-flex align-items-center bg-white border rounded px-3 py-2">
                            <i class="fa-solid fa-wrench text-warning me-2"></i> Ready Sparepart
                        </div>
                        <div class="d-flex align-items-center bg-white border rounded px-3 py-2">
                            <i class="fa-solid fa-truck-fast text-primary me-2"></i> Siap Kirim
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="d-grid gap-3 d-md-flex">
                        @php
                            $waMessage = rawurlencode("Halo Roket Mini Moto, saya mau tanya ketersediaan stok untuk produk *" . $product->name . "* ini. Apakah masih ada?");
                        @endphp
                        <a href="https://wa.me/6282335465000?text={{ $waMessage }}" target="_blank" class="btn btn-primary-custom btn-lg rounded-pill px-5 fw-bold d-flex align-items-center justify-content-center">
                            <i class="fa-brands fa-whatsapp fs-4 me-2"></i> Pesan Sekarang
                        </a>
                        <a href="{{ route('home') }}#produk" class="btn btn-outline-secondary btn-lg rounded-pill px-4 fw-bold d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ strip_tags($product->description ?? '') }}",
    "image": "{{ $product->image ? asset('storage/'.$product->image) : ($product->photo ? asset('storage/'.$product->photo) : asset('assets/images/no-image.png')) }}",
    "category": "{{ $product->category->name ?? 'Kendaraan Mini' }}",
    "offers": {
        "@type": "Offer",
        "priceCurrency": "IDR",
        "price": "{{ $product->price }}",
        "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
        "url": "{{ url()->current() }}",
        "seller": {
            "@type": "LocalBusiness",
            "name": "Roket Mini Moto",
            "url": "{{ url('/') }}"
        }
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "{{ route('home') }}"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "Produk",
            "item": "{{ route('home') }}#produk"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "{{ $product->name }}",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>
@endpush
