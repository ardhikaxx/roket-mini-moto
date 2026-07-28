@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <header id="beranda" class="hero-section">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <h1>Pusat Grosir Sepeda Listrik, Trail Mini & Mobil Aki Termurah di Bondowoso</h1>
                        <p>Roket Mini Moto adalah dealer dan pusat penjualan <strong>sepeda listrik</strong> (Uwinfly, Exotic, dll), <strong>mobil aki anak</strong>, <strong>trail mini</strong>, dan <strong>ATV</strong> dengan harga pabrik dan full garansi di Bondowoso, Situbondo, dan Jember.</p>
                        
                        <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                            <a href="#produk" class="btn btn-primary-custom">Lihat Katalog</a>
                            <!-- WhatsApp Number Placeholder: 6282335465000 -->
                            <a href="https://wa.me/6282335465000?text=Halo,%20saya%20tertarik%20dengan%20produk%20di%20Roket%20Mini%20Moto" target="_blank" class="btn btn-outline-custom text-white border-white">
                                <i class="fa-brands fa-whatsapp"></i> Tanya via WhatsApp
                            </a>
                        </div>

                        <div class="trust-badges">
                            <div class="badge-item">
                                <i class="fa-solid fa-tags"></i> Harga Terjangkau
                            </div>
                            <div class="badge-item">
                                <i class="fa-solid fa-shield-halved"></i> Full Garansi
                            </div>
                            <div class="badge-item">
                                <i class="fa-solid fa-headset"></i> Siap Konsultasi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Section Kategori Produk -->
    <section id="kategori" class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Kategori Pilihan</h2>
                <p class="text-muted">Pilih jenis kendaraan mini yang sesuai dengan keinginan dan kebutuhan Anda.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="category-card" onclick="document.querySelector('[data-filter=\'trail\']').click(); window.location.href='#produk';">
                        <div class="category-icon">
                            <i class="fa-solid fa-motorcycle"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Mini Trail</h5>
                        <p class="text-muted small mb-0">Sporty & Tangguh</p>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="category-card" onclick="document.querySelector('[data-filter=\'mobil\']').click(); window.location.href='#produk';">
                        <div class="category-icon">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Mobil Aki</h5>
                        <p class="text-muted small mb-0">Aman & Menyenangkan</p>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="category-card" onclick="document.querySelector('[data-filter=\'atv\']').click(); window.location.href='#produk';">
                        <div class="category-icon">
                            <i class="fa-solid fa-truck-monster"></i>
                        </div>
                        <h5 class="fw-bold mb-1">ATV Mini</h5>
                        <p class="text-muted small mb-0">Petualangan Seru</p>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="category-card" onclick="document.querySelector('[data-filter=\'sepeda-listrik\']').click(); window.location.href='#produk';">
                        <div class="category-icon">
                            <i class="fa-solid fa-bicycle"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Sepeda Listrik</h5>
                        <p class="text-muted small mb-0">E-Bike & Moped</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Produk Unggulan -->
    <section id="produk" class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Produk Unggulan</h2>
                <p class="text-muted">Jelajahi koleksi kendaraan mini terbaik kami dengan harga bersahabat dan full garansi.</p>
                
                <!-- Live Search Bar -->
                <div class="row justify-content-center mt-4 mb-3">
                    <div class="col-md-7 col-lg-5">
                        <div class="input-group shadow-sm" style="border-radius: 50px; overflow: hidden; border: 2px solid var(--primary-color);">
                            <span class="input-group-text bg-white border-0 ps-3 text-muted">
                                <i class="fa-solid fa-magnifying-glass" style="color: var(--primary-color);"></i>
                            </span>
                            <input type="text" id="productSearchInput" class="form-control border-0 py-2 shadow-none" placeholder="Cari produk (misal: Mini Trail, Mobil Aki, ATV)..." style="font-size: 0.95rem;">
                            <button class="btn btn-primary-custom px-3 border-0" type="button" id="clearSearchBtn" style="display: none; border-radius: 0;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category Filter Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                    <button class="filter-btn active" data-filter="all">Semua Kategori</button>
                    @foreach(\App\Models\Category::all() as $category)
                        <button class="filter-btn" data-filter="{{ $category->slug }}">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>

            <div class="row g-4" id="productGrid">
                
                @forelse($products as $product)
                <!-- Dynamic Product -->
                <div class="col-12 col-md-4 col-lg-3 product-item" data-category="{{ $product->category->slug ?? 'umum' }}" style="transition: all 0.3s ease;">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <span class="badge-category">{{ $product->category->name ?? 'Produk' }}</span>
                            <img src="{{ $product->photo ? asset('storage/'.$product->photo) : asset('assets/images/no-image.png') }}" alt="{{ $product->name }}" class="img-fluid" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/images/no-image.png') }}';">
                        </div>
                        <div class="product-body">
                            <h3 class="product-title fw-bold">{{ $product->name }}</h3>
                            <p class="product-desc">{{ Str::limit($product->description ?? 'Deskripsi produk belum tersedia.', 80) }}</p>
                            <div class="d-flex align-items-center mb-3 text-success small fw-bold">
                                <i class="fa-solid fa-check-circle me-1"></i> Full Garansi
                            </div>
                            <h5 class="fw-bold mb-3" style="color: #000;">Rp {{ number_format($product->price, 0, ',', '.') }}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="https://wa.me/6282335465000?text=Halo%20min,%20saya%20mau%20tanya%20produk%20{{ rawurlencode($product->name) }}" target="_blank" class="btn btn-primary-custom w-100 me-2 py-2" style="font-size: 0.9rem;">Tanya Stok</a>
                                <a href="{{ route('produk.detail', ['id' => $product->id]) }}" class="btn btn-outline-custom px-3 py-2 btn-detail" title="Lihat Detail"><i class="fa-solid fa-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Belum ada produk yang tersedia saat ini.</p>
                </div>
                @endforelse

                <!-- Empty match message for search/filter -->
                <div class="col-12 text-center py-5" id="noProductMatchMsg" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass-minus fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">Produk Tidak Ditemukan</h5>
                    <p class="text-muted">Tidak ada produk yang cocok dengan kata kunci pencarian atau kategori yang Anda pilih.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Section Keunggulan -->
    <section id="keunggulan" class="section-padding bg-light-custom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <h2 class="fw-bold mb-4">Mengapa Memilih Roket Mini Moto?</h2>
                    <p class="text-muted mb-4">Kami berkomitmen memberikan pengalaman belanja kendaraan mini terbaik di Bondowoso dengan pelayanan maksimal dan kualitas produk terjamin.</p>
                    <a href="https://wa.me/6282335465000" class="btn btn-primary-custom">Konsultasi Sekarang</a>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fa-solid fa-tags"></i></div>
                                <div>
                                    <h5 class="fw-bold">Harga Terjangkau</h5>
                                    <p class="small text-muted mb-0">Dapatkan penawaran harga terbaik dan bersaing.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                                <div>
                                    <h5 class="fw-bold">Full Garansi</h5>
                                    <p class="small text-muted mb-0">Semua produk dilindungi dengan garansi toko resmi.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                                <div>
                                    <h5 class="fw-bold">Produk Beragam</h5>
                                    <p class="small text-muted mb-0">Pilihan terlengkap dari trail, mobil aki, hingga ATV.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="feature-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                                <div>
                                    <h5 class="fw-bold">Mudah Ditemukan</h5>
                                    <p class="small text-muted mb-0">Lokasi strategis di pusat kota Bondowoso.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner Service & Spare Parts -->
    <section class="py-5 bg-dark-custom text-white position-relative" style="overflow: hidden;">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3 gap-lg-4 reveal">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; flex-shrink: 0; background-color: var(--primary-color) !important;">
                            <i class="fa-solid fa-screwdriver-wrench fs-2"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-2">Layanan Servis & Spare Part Lengkap</h3>
                            <p class="mb-0 opacity-75">Bukan sekadar menjual, kami juga menyediakan layanan purna jual profesional. Tersedia suku cadang asli dan mekanik berpengalaman untuk merawat kendaraan mini Anda agar selalu prima.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-start text-lg-end reveal mt-4 mt-lg-0">
                    <a href="https://wa.me/6282335465000?text=Halo,%20saya%20mau%20tanya%20layanan%20servis%20dan%20spare%20part" target="_blank" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold">
                        <i class="fa-brands fa-whatsapp me-2"></i> Booking Servis
                    </a>
                </div>
            </div>
        </div>
        <!-- Decorative Background Icon -->
        <i class="fa-solid fa-gear position-absolute" style="font-size: 20rem; right: -5%; top: -50%; opacity: 0.05; color: white;"></i>
    </section>

    <!-- Section Tentang Toko -->
    <section id="tentang" class="section-padding">
        <div class="container">
            <div class="row align-items-center flex-column-reverse flex-lg-row">
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="row g-3">
                        <div class="col-6">
                            <img src="{{ asset('assets/images/mini-trail-1.jpg') }}" alt="Tentang Roket Mini Moto 1" class="about-img mb-3" loading="lazy">
                        </div>
                        <div class="col-6 mt-4">
                            <img src="{{ asset('assets/images/mobil-aki-1.jpg') }}" alt="Tentang Roket Mini Moto 2" class="about-img" loading="lazy">
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1">
                    <h2 class="fw-bold mb-4">Tentang Roket Mini Moto</h2>
                    <p class="text-muted">Roket Mini Moto hadir sebagai toko kendaraan mini terpercaya di Bondowoso. Kami menyediakan berbagai pilihan mini trail, mobil aki, ATV mini, dan kendaraan mini lainnya yang sangat cocok untuk hiburan anak, remaja, hingga kebutuhan aktivitas outdoor keluarga.</p>
                    <p class="text-muted">Kami percaya bahwa kebahagiaan tidak harus mahal. Oleh karena itu, kami menghadirkan produk dengan harga yang sangat terjangkau, kualitas terbaik di kelasnya, serta dukungan <strong>full garansi</strong> untuk memastikan kenyamanan Anda dalam berbelanja.</p>
                    <p class="text-muted mb-0">Datang dan buktikan sendiri koleksi kami yang beragam, atau konsultasikan kebutuhan Anda kepada tim kami dengan ramah dan profesional.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Statistik (Animated Counter) -->
    <section class="py-5 bg-dark-custom text-white position-relative" style="overflow: hidden;">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row text-center g-4">
                <div class="col-6 col-md-3 reveal">
                    <i class="fa-solid fa-motorcycle fs-1 text-primary-custom mb-3"></i>
                    <h2 class="fw-bold display-5 mb-1"><span class="counter" data-target="500">0</span>+</h2>
                    <p class="text-white-50 mb-0">Unit Terjual</p>
                </div>
                <div class="col-6 col-md-3 reveal" style="transition-delay: 100ms;">
                    <i class="fa-solid fa-shield-halved fs-1 text-primary-custom mb-3"></i>
                    <h2 class="fw-bold display-5 mb-1"><span class="counter" data-target="100">0</span>%</h2>
                    <p class="text-white-50 mb-0">Produk Bergaransi</p>
                </div>
                <div class="col-6 col-md-3 reveal" style="transition-delay: 200ms;">
                    <i class="fa-solid fa-boxes-stacked fs-1 text-primary-custom mb-3"></i>
                    <h2 class="fw-bold display-5 mb-1"><span class="counter" data-target="15">0</span>+</h2>
                    <p class="text-white-50 mb-0">Pilihan Model Baru</p>
                </div>
                <div class="col-6 col-md-3 reveal" style="transition-delay: 300ms;">
                    <i class="fa-solid fa-headset fs-1 text-primary-custom mb-3"></i>
                    <h2 class="fw-bold display-5 mb-1"><span class="counter" data-target="24">0</span>/7</h2>
                    <p class="text-white-50 mb-0">Layanan Konsultasi</p>
                </div>
            </div>
        </div>
        <!-- Decorative bg -->
        <i class="fa-solid fa-chart-line position-absolute" style="font-size: 25rem; left: -5%; bottom: -40%; opacity: 0.05; color: white;"></i>
    </section>

    <!-- Section Testimoni -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <h2 class="fw-bold">Apa Kata Pelanggan Kami?</h2>
                <p class="text-muted">Ratusan pelanggan telah mempercayakan hiburan keluarga mereka kepada kami.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: var(--border-radius);">
                        <div class="d-flex text-warning mb-3">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="fst-italic text-muted mb-4">"Beli mobil aki buat kado ulang tahun anak di sini. Harganya paling murah di Bondowoso, kualitasnya bagus dan dapet garansi toko. Pelayanannya ramah banget!"</p>
                        <div class="d-flex align-items-center mt-auto">
                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 50px; height: 50px; background-color: var(--primary-color);">BW</div>
                            <div>
                                <h6 class="fw-bold mb-0">Bapak Wahyu</h6>
                                <small class="text-muted">Bondowoso</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal" style="transition-delay: 100ms;">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: var(--border-radius);">
                        <div class="d-flex text-warning mb-3">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="fst-italic text-muted mb-4">"Cari mini trail muter-muter akhirnya ketemu Roket Mini Moto. Barangnya ready stock banyak pilihan, mekaniknya juga pinter jelasin cara perawatannya. Sangat puas!"</p>
                        <div class="d-flex align-items-center mt-auto">
                            <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 50px; height: 50px;">AD</div>
                            <div>
                                <h6 class="fw-bold mb-0">Andi</h6>
                                <small class="text-muted">Situbondo</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal" style="transition-delay: 200ms;">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: var(--border-radius);">
                        <div class="d-flex text-warning mb-3">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="fst-italic text-muted mb-4">"Top markotop! Beli ATV mini buat main di kebun. Sempat bingung soal akinya, tinggal chat WA langsung direspon dan dipandu sampai beres. Rekomendasi banget."</p>
                        <div class="d-flex align-items-center mt-auto">
                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 50px; height: 50px; background-color: var(--muted-color);">IB</div>
                            <div>
                                <h6 class="fw-bold mb-0">Ibu Budi</h6>
                                <small class="text-muted">Jember</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section FAQ -->
    <section class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0 reveal">
                    <h2 class="fw-bold mb-4">Pertanyaan Umum (FAQ)</h2>
                    <p class="text-muted mb-4">Temukan jawaban cepat mengenai produk, pengiriman, dan layanan garansi dari Roket Mini Moto.</p>
                    <div class="bg-light-custom p-4 rounded text-center">
                        <i class="fa-regular fa-comments text-primary-custom mb-3" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold">Punya Pertanyaan Lain?</h5>
                        <p class="text-muted small">Jangan ragu, tanyakan langsung pada admin kami yang siap membantu Anda dengan ramah.</p>
                        <a href="https://wa.me/6282335465000" class="btn btn-outline-custom btn-sm rounded-pill mt-2">Chat Admin</a>
                    </div>
                </div>
                <div class="col-lg-7 reveal">
                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--border-radius); overflow: hidden;">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Bagaimana cara klaim garansi produk?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Sangat mudah! Jika terjadi kendala pada mesin atau kelistrikan (bukan <i>human error</i>) dalam masa garansi, Anda dapat membawa unit ke bengkel Roket Mini Moto beserta nota pembelian, dan mekanik kami akan menanganinya tanpa biaya servis.
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 2 -->
                        <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--border-radius); overflow: hidden;">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Apakah bisa kirim ke luar kota?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Ya, kami melayani pengiriman ke seluruh daerah menggunakan jasa ekspedisi terpercaya dengan pengemasan super aman. Khusus area Bondowoso dan sekitarnya, tersedia penawaran pengiriman menarik.
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 3 -->
                        <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: var(--border-radius); overflow: hidden;">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Apakah tersedia jaminan ketersediaan spare part?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Tentu saja. Sebagai komitmen toko, Roket Mini Moto menyediakan suku cadang <strong>(spare parts)</strong> lengkap, mulai dari aki, ban pacul, kampas rem, hingga karburator mini. Perawatan rutin jadi sangat mudah.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Proses Pembelian -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Cara Mudah Membeli</h2>
                <p class="text-muted">Dapatkan kendaraan mini impian Anda hanya dalam beberapa langkah sederhana.</p>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h5 class="fw-bold">1. Pilih Produk</h5>
                        <p class="text-muted small">Lihat katalog kami dan temukan yang paling cocok.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <h5 class="fw-bold">2. Konsultasi</h5>
                        <p class="text-muted small">Hubungi kami via WA untuk diskusi dan ketersediaan.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <h5 class="fw-bold">3. Cek Harga</h5>
                        <p class="text-muted small">Dapatkan informasi harga terbaik dari kami.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <h5 class="fw-bold">4. Pembelian</h5>
                        <p class="text-muted small">Bawa pulang kendaraan mini impian Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-overlay"></div>
        <div class="container cta-content">
            <h2 class="fw-bold display-5 mb-4">Siap Membawa Pulang Kendaraan Mini Impianmu?</h2>
            <p class="lead mb-5 max-w-700 mx-auto" style="max-width: 700px;">Jangan ragu untuk menghubungi tim kami. Kami siap membantu Anda memilih kendaraan mini yang paling tepat dan sesuai dengan budget Anda.</p>
            <a href="https://wa.me/6282335465000" class="btn btn-primary-custom btn-lg">
                <i class="fa-brands fa-whatsapp me-2"></i> Konsultasi Sekarang
            </a>
        </div>
    </section>

    <!-- Section Lokasi (Maps) -->
    <section class="section-padding bg-light-custom" id="lokasi">
        <div class="container">
            <div class="row text-center mb-5 reveal">
                <div class="col-lg-6 mx-auto">
                    <h2 class="fw-bold">Lokasi Toko Kami</h2>
                    <p class="text-muted">Kunjungi toko fisik kami untuk melihat dan mencoba langsung kendaraan mini impian Anda.</p>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-4 mb-5 mb-lg-0 reveal">
                    <div class="card border-0 shadow-lg p-4 bg-dark-custom text-white position-relative overflow-hidden" style="border-radius: var(--border-radius);">
                        <!-- Decorative bg icon -->
                        <i class="fa-solid fa-map-pin position-absolute" style="font-size: 15rem; right: -15%; bottom: -10%; opacity: 0.05; color: white;"></i>
                        
                        <div class="position-relative" style="z-index: 2;">
                            <h4 class="fw-bold mb-4 d-flex align-items-center">
                                <i class="fa-solid fa-store text-primary-custom me-2"></i> Roket Mini Moto
                            </h4>
                            <hr class="border-secondary mb-4">
                            <ul class="list-unstyled mb-4">
                                <li class="d-flex mb-4">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 45px; height: 45px;">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block mb-1 text-primary-custom">Alamat Lengkap</strong>
                                        <span class="small" style="color: rgba(255,255,255,0.85); line-height: 1.6; display: block;">Utara Hotel Baru, Jln. Kartini No.41, Pegadaian, Blindungan, Kec. Bondowoso, Jawa Timur 68212</span>
                                    </div>
                                </li>
                                <li class="d-flex mb-4">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 45px; height: 45px;">
                                        <i class="fa-solid fa-clock"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block mb-1 text-primary-custom">Jam Operasional</strong>
                                        <span class="small" style="color: rgba(255,255,255,0.85);">Senin - Sabtu: 08.00 - 22.00 WIB<br>Minggu: 08.00 - 21.00 WIB</span>
                                    </div>
                                </li>
                            </ul>
                            <a href="https://wa.me/6282335465000" target="_blank" class="btn w-100 fw-bold py-3 rounded-pill shadow" style="background-color: #25d366; color: white; border: none; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                <i class="fa-brands fa-whatsapp fs-5 me-2 align-middle"></i> <span class="align-middle">Chat via WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 reveal" style="transition-delay: 200ms;">
                    <div class="card border-0 shadow-lg p-2 bg-white" style="border-radius: var(--border-radius);">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d814.9317170278854!2d113.82372810864346!3d-7.9105452155397495!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6dde6cb9cf9a9%3A0x12b6ad7fa0f55022!2sTRAIL%20Mini%20MURAH%20Bondowoso.toko%20sepeda%20listrik.bengkel%20sepeda%20listrik!5e0!3m2!1sid!2sid!4v1784932303504!5m2!1sid!2sid" width="100%" height="450" style="border:0; border-radius: calc(var(--border-radius) - 4px);" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
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
  "@@type": "LocalBusiness",
  "name": "Roket Mini Moto",
  "image": "{{ asset('assets/images/mobil-aki-1.jpg') }}",
  "@@id": "{{ url('/') }}",
  "url": "{{ url('/') }}",
  "telephone": "+6282335465000",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "Utara Hotel Baru, Jln. Kartini No.41, Pegadaian, Blindungan",
    "addressLocality": "Bondowoso",
    "addressRegion": "Jawa Timur",
    "postalCode": "68212",
    "addressCountry": "ID"
  },
  "geo": {
    "@@type": "GeoCoordinates",
    "latitude": -7.9158,
    "longitude": 113.8222
  },
  "openingHoursSpecification": {
    "@@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday"
    ],
    "opens": "08:00",
    "closes": "20:00"
  },
  "sameAs": [
    "https://www.instagram.com/roket_mini_moto/",
    "https://www.tiktok.com/@trailminianak"
  ],
  "description": "Roket Mini Moto adalah pusat penjualan dan grosir sepeda listrik, mobil aki anak, trail mini, dan ATV termurah di Bondowoso, Situbondo, dan Jember."
}
</script>
@endpush
