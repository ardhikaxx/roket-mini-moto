@extends('layouts.app')

@section('title', 'Detail Produk - Roket Mini Moto')
@section('navbar_class', 'navbar-dark navbar-custom sticky-top')
@section('navbar_style', 'background-color: rgba(26, 26, 26, 0.95); padding: 10px 0;')

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-light-custom py-3 border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}#produk" class="text-decoration-none text-muted">Produk</a></li>
                    <li class="breadcrumb-item active fw-bold" aria-current="page" id="breadcrumbName">Detail Produk</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Detail Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row gx-5">
                <!-- Product Image -->
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <img src="{{ asset('assets/images/mini-trail-1.jpg') }}" id="detailImg" class="w-100 object-fit-cover" style="height: 500px;" alt="Product Image" loading="lazy">
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <span class="badge bg-primary px-3 py-2 rounded-pill" id="detailCategory">Kategori</span>
                    </div>
                    <h2 class="fw-bold mb-3" id="detailTitle">Memuat Produk...</h2>
                    <h3 class="text-primary-custom fw-bold mb-4" id="detailPrice">Rp -</h3>
                    
                    <p class="text-muted mb-4" style="line-height: 1.8;" id="detailDesc">
                        Deskripsi produk sedang dimuat...
                    </p>

                    <!-- Spesifikasi -->
                    <div class="mb-5">
                        <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-list-check me-2 text-primary-custom"></i>Spesifikasi Utama</h5>
                        <div id="detailSpecs">
                            <!-- Injected by JS -->
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
                        <a href="#" id="btnWaBeli" target="_blank" class="btn btn-primary-custom btn-lg rounded-pill px-5 fw-bold d-flex align-items-center justify-content-center">
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
<script>
    const productsDB = {
        'mini-trail-50cc': {
            title: 'Mini Trail 50cc 2 Tak',
            category: 'Mini Trail',
            price: 'Rp 2.500.000',
            image: '{{ asset("assets/images/mini-trail-1.jpg") }}',
            desc: 'Mini Trail 50cc 2 Tak sangat digemari karena tarikannya yang enteng dan responsif. Desain bodinya ramping sehingga sangat cocok dan aman digunakan oleh anak-anak usia 6 hingga 10 tahun untuk belajar mengendarai motor di medan offroad ringan maupun di halaman rumah.',
            specs: {
                'Tipe Mesin': '50cc, 2 Tak, Silinder Tunggal',
                'Sistem Starter': 'Pull Start (Tarik)',
                'Bahan Bakar': 'Bensin Campur (Rasio 25:1)',
                'Kapasitas Tangki': '1.5 Liter',
                'Beban Maksimal': '50 kg',
                'Sistem Pengereman': 'Cakram (Depan & Belakang)'
            }
        },
        'mobil-aki-rubicon': {
            title: 'Mobil Aki Jeep Rubicon',
            category: 'Mobil Aki',
            price: 'Rp 1.850.000',
            image: '{{ asset("assets/images/mobil-aki-1.jpg") }}',
            desc: 'Hadirkan keseruan bermain anak dengan Mobil Aki berdesain Jeep Rubicon yang gagah ini. Mobil ini dilengkapi dengan remote control (kendali jarak jauh oleh orang tua) dan pedal gas manual untuk anak, memastikan keamanan ganda. Cocok untuk jalanan rata maupun paving block.',
            specs: {
                'Tipe Baterai': '12V 7Ah (Awet Tahan Lama)',
                'Motor Penggerak': '2 Gearbox (Tenaga Lebih Kuat)',
                'Sistem Kendali': 'Manual Injak Pedal & Remote Control',
                'Beban Maksimal': '35 kg',
                'Fitur Hiburan': 'Bluetooth, Pemutar Musik MP3, Lampu LED Nyala'
            }
        },
        'atv-mini-110cc': {
            title: 'ATV Hunter 110cc Matic',
            category: 'ATV Mini',
            price: 'Rp 7.500.000',
            image: '{{ asset("assets/images/atv-mini-1.jpg") }}',
            desc: 'ATV kelas menengah yang tangguh dengan mesin 110cc 4 Tak otomatis. Diciptakan khusus untuk melibas medan tanah, lumpur, maupun rerumputan tebal. Sangat stabil dikendarai oleh remaja hingga orang dewasa, cocok untuk hiburan keluarga atau penyewaan di tempat wisata.',
            specs: {
                'Tipe Mesin': '110cc, 4 Tak, Otomatis (Matic)',
                'Sistem Starter': 'Elektrik (Tinggal Pencet Tombol)',
                'Bahan Bakar': 'Bensin Murni (Pertamax/Pertalite)',
                'Beban Maksimal': '100 kg',
                'Ukuran Ban': 'Ring 7 atau 8 Inch (Ban Pacul)',
                'Sistem Pengereman': 'Cakram Hidrolik (Belakang), Tromol (Depan)'
            }
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Capture id parameter either from query string ?id=... or a blade variable if we set one up
        const urlParams = new URLSearchParams(window.location.search);
        // Look in query param 'id', if not check the path, for simplicity we just read URL search params as in demo
        // For blade, since route has param /produk/{id}, let's pass it via blade
        const productId = '{{ request()->route("id") ?? "" }}' || urlParams.get('id') || 'mini-trail-50cc';
        
        const product = productsDB[productId];
        
        if(product) {
            document.title = product.title + ' - Roket Mini Moto';
            document.getElementById('breadcrumbName').innerText = product.title;
            document.getElementById('detailImg').src = product.image;
            document.getElementById('detailCategory').innerText = product.category;
            document.getElementById('detailTitle').innerText = product.title;
            document.getElementById('detailPrice').innerText = product.price;
            document.getElementById('detailDesc').innerText = product.desc;
            
            // Build specs list
            let specsHTML = '';
            for (const [key, value] of Object.entries(product.specs)) {
                specsHTML += `
                    <div class="row mb-2">
                        <div class="col-5 col-md-4 text-muted">${key}</div>
                        <div class="col-7 col-md-8 fw-bold">${value}</div>
                    </div>
                `;
            }
            document.getElementById('detailSpecs').innerHTML = specsHTML;

            // Set WA link
            const waMessage = encodeURIComponent(`Halo Roket Mini Moto, saya tertarik untuk membeli produk *${product.title}*. Apakah stoknya masih tersedia?`);
            document.getElementById('btnWaBeli').href = `https://wa.me/6282335465000?text=${waMessage}`;
        } else {
            document.getElementById('detailTitle').innerText = "Produk tidak ditemukan";
            document.getElementById('detailDesc').innerText = "Silakan kembali ke halaman utama untuk melihat katalog produk kami.";
        }
    });
</script>
@endpush
