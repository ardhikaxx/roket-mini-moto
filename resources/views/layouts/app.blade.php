<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Roket Mini Moto | Trail Mini dan Mobil Aki Murah Bondowoso')</title>
    <meta name="description" content="@yield('meta_description', 'Roket Mini Moto menyediakan mini trail, mobil aki, ATV mini, dan kendaraan mini lainnya dengan harga terjangkau dan full garansi di Bondowoso.')">
    <meta name="keywords" content="mini trail bondowoso, mobil aki anak, atv mini, motor mini murah, roket mini moto">
    
    <!-- Open Graph Metadata -->
    <meta property="og:title" content="Roket Mini Moto | Trail Mini dan Mobil Aki Murah Bondowoso">
    <meta property="og:description" content="Toko kendaraan mini anak dan remaja termurah di Bondowoso. Full Garansi!">
    <meta property="og:type" content="website">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg @yield('navbar_class', 'navbar-custom')">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="fa-solid fa-motorcycle text-primary-custom fs-3"></i>
                <div class="d-flex flex-column justify-content-center">
                    <span style="line-height: 1;">ROKET MINI MOTO</span>
                    <span style="font-size: 0.65rem; text-transform: capitalize; letter-spacing: 0; font-weight: 500; color: rgba(255,255,255,0.85); margin-top: 4px; font-family: 'Inter', sans-serif;">Trail Mini dan Mobil Aki Murah Bondowoso</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('home') }}#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#kategori">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#produk">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#keunggulan">Keunggulan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#tentang">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#lokasi">Lokasi</a>
                    </li>
                    <!-- WhatsApp Number Placeholder: 6282335465000 -->
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0 d-flex flex-wrap gap-2 align-items-center">
                        <a class="btn btn-primary-custom btn-sm" href="https://wa.me/6282335465000?text=Halo%20Roket%20Mini%20Moto,%20saya%20ingin%20bertanya%20mengenai%20produk%20kendaraan%20mini." target="_blank">
                            <i class="fa-brands fa-whatsapp me-1"></i> Hubungi Kami
                        </a>
                        @auth
                            <a class="btn btn-outline-light btn-sm fw-semibold px-3" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isKepalaToko() ? route('admin.reports.index') : route('karyawan.dashboard')) }}">
                                <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
                            </a>
                        @else
                            <a class="btn btn-outline-light btn-sm fw-semibold px-3" href="{{ route('login') }}" style="border-color: rgba(255,255,255,0.35); color: #ffffff;">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row text-start">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <h4 class="text-white fw-bold mb-4 d-flex align-items-center justify-content-start gap-2">
                        <i class="fa-solid fa-motorcycle text-primary-custom fs-3"></i> 
                        <div class="d-flex flex-column justify-content-center text-start">
                            <span style="line-height: 1; text-transform: uppercase;">ROKET MINI MOTO</span>
                            <span style="font-size: 0.75rem; text-transform: capitalize; letter-spacing: 0; font-weight: 500; color: rgba(255,255,255,0.85); margin-top: 6px; font-family: 'Inter', sans-serif;">Trail Mini dan Mobil Aki Murah Bondowoso</span>
                        </div>
                    </h4>
                    <p class="text-white">Solusi tepat untuk kendaraan mini berkualitas dengan harga terjangkau dan full garansi.</p>
                    <div class="social-icons mt-4">
                        <!-- Placeholder Social Media -->
                        <a href="https://www.instagram.com/roket_mini_moto/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://www.tiktok.com/@trailminianak" target="_blank"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-5 mb-lg-0 offset-lg-2">
                    <h5 class="mb-4">Navigasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}#beranda">Beranda</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#kategori">Kategori</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#produk">Produk</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#tentang">Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-4">Kontak Kami</h5>
                    <ul class="list-unstyled text-white d-inline-block text-start">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fa-solid fa-location-dot mt-1 me-3 text-primary-custom"></i>
                            <span>Utara Hotel Baru, Jln. Kartini No.41, Pegadaian, Blindungan,<br>Kec. Bondowoso, Jawa Timur 68212</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fa-brands fa-whatsapp me-3 text-primary-custom"></i>
                            <span>+62 823-3546-5000</span>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary mt-5 mb-4">
            <div class="text-start text-lg-center text-white small">
                &copy; 2026 Roket Mini Moto. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/6282335465000?text=Halo%20Roket%20Mini%20Moto,%20saya%20ingin%20bertanya%20mengenai%20produk%20kendaraan%20mini%20yang%20tersedia." class="wa-float" target="_blank" title="Chat dengan kami">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <!-- Global Image Error Fallback Script -->
    <script>
        document.addEventListener('error', function(event) {
            if (event.target.tagName === 'IMG') {
                const fallback = "{{ asset('assets/images/no-image.png') }}";
                if (event.target.src !== fallback) {
                    event.target.src = fallback;
                }
            }
        }, true);
    </script>

    @stack('scripts')
</body>
</html>
