<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - Roket Mini Moto</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #e63946;
            --primary-hover: #c42b37;
            --dark: #0f172a;
            --dark-card: #1e293b;
            --light-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-lg: 24px;
            --radius-md: 12px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 24px 14px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Background Effects */
        .bg-glow-1 {
            position: fixed;
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(230, 57, 70, 0.28) 0%, rgba(230, 57, 70, 0) 70%);
            z-index: 0;
            pointer-events: none;
            filter: blur(50px);
        }
        
        .bg-glow-2 {
            position: fixed;
            bottom: -10%;
            right: -10%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.22) 0%, rgba(59, 130, 246, 0) 70%);
            z-index: 0;
            pointer-events: none;
            filter: blur(60px);
        }

        .login-card-container {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            border-radius: var(--radius-lg);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            position: relative;
            z-index: 1;
            display: flex;
            min-height: 560px;
        }

        /* Hero Split Panel */
        .login-hero-panel {
            flex: 1;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.88), rgba(196, 43, 55, 0.92)), 
                        url("{{ asset('assets/images/hero-bg.jpg') }}") center/cover no-repeat;
            padding: 48px 40px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .login-hero-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(230, 57, 70, 0.35), transparent 65%);
            pointer-events: none;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 8px 18px;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            width: fit-content;
        }

        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 14px;
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
        }

        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }

        .feature-pill {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Form Panel */
        .login-form-panel {
            flex: 1.1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-header h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.65rem;
            margin-bottom: 6px;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 28px;
        }

        /* Form Controls */
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 1rem;
            z-index: 4;
            transition: color 0.2s ease;
        }

        .input-group-custom .form-control-custom {
            width: 100%;
            padding: 12px 16px 12px 46px;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            color: var(--text-dark);
            background-color: #f8fafc;
            transition: all 0.25s ease;
        }

        .input-group-custom .form-control-custom:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.12);
        }

        .input-group-custom .form-control-custom:focus + .input-icon,
        .input-group-custom:focus-within .input-icon {
            color: var(--primary);
        }

        .toggle-pin-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 1rem;
            z-index: 4;
            transition: color 0.2s ease;
        }

        .toggle-pin-btn:hover {
            color: var(--primary);
        }

        /* PIN Digit Indicator helper */
        .pin-indicator {
            display: flex;
            gap: 6px;
            margin-top: 8px;
            justify-content: flex-end;
            align-items: center;
        }
        
        .pin-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #cbd5e1;
            transition: all 0.2s ease;
        }

        .pin-dot.active {
            background-color: var(--primary);
            transform: scale(1.25);
        }

        /* Custom Alert */
        .alert-custom {
            border: none;
            border-radius: var(--radius-md);
            background-color: #fef2f2;
            border-left: 4px solid var(--primary);
            color: #991b1b;
            padding: 12px 16px;
            font-size: 0.88rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, #c42b37 100%);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            padding: 13px 20px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.98rem;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(230, 57, 70, 0.35);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #f04351 0%, #b32430 100%);
            box-shadow: 0 6px 20px rgba(230, 57, 70, 0.5);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(230, 57, 70, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Footer Link */
        .form-footer {
            margin-top: 28px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .back-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link:hover {
            color: var(--primary);
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .login-card-container {
                flex-direction: column;
                max-width: 480px;
            }
            .login-hero-panel {
                padding: 32px 28px;
                min-height: auto;
            }
            .hero-title {
                font-size: 1.6rem;
            }
            .login-form-panel {
                padding: 36px 28px;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Glowing Orbs -->
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="container d-flex justify-content-center align-items-center">
        <div class="login-card-container">
            
            <!-- Left Hero Section -->
            <div class="login-hero-panel">
                <div>
                    <div class="brand-badge mb-4">
                        <i class="fa-solid fa-rocket text-warning"></i>
                        <span>ROKET MINI MOTO</span>
                    </div>
                    <h2 class="hero-title">Sistem Manajemen & Operasional</h2>
                    <p class="hero-subtitle">
                        Platform internal untuk mengelola persediaan produk, transaksi kasir, laporan omzet, dan pengguna toko Roket Mini Moto Bondowoso.
                    </p>
                </div>

                <div>
                    <div class="feature-pills">
                        <span class="feature-pill"><i class="fa-solid fa-shield-halved me-1 text-warning"></i> Keamanan PIN 4 Digit</span>
                        <span class="feature-pill"><i class="fa-solid fa-motorcycle me-1 text-danger-subtle"></i> Mini Trail & ATV</span>
                        <span class="feature-pill"><i class="fa-solid fa-car-side me-1 text-info-subtle"></i> Mobil Aki Anak</span>
                    </div>
                    <div class="mt-4 pt-2 text-white-50 small">
                        &copy; {{ date('Y') }} Roket Mini Moto. All rights reserved.
                    </div>
                </div>
            </div>

            <!-- Right Form Section -->
            <div class="login-form-panel">
                <div class="form-header">
                    <h4>Selamat Datang 👋</h4>
                    <p>Masukkan username dan 4 digit PIN untuk masuk</p>
                </div>

                @if(session('error'))
                    <div class="alert-custom">
                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-custom">
                        <i class="fa-solid fa-circle-exclamation fs-5"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <span>{{ $error }}</span><br>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf
                    
                    <!-- Username Input -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username Akun</label>
                        <div class="input-group-custom">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input 
                                type="text" 
                                id="username"
                                name="username" 
                                class="form-control-custom" 
                                placeholder="Masukkan username"
                                value="{{ old('username') }}"
                                required 
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    <!-- PIN Input -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="pin" class="form-label mb-0">PIN Keamanan</label>
                            <span class="text-muted" style="font-size: 0.78rem;">4 Digit Angka</span>
                        </div>
                        <div class="input-group-custom">
                            <i class="fa-solid fa-key input-icon"></i>
                            <input 
                                type="password" 
                                id="pin"
                                name="pin" 
                                class="form-control-custom" 
                                placeholder="••••"
                                maxlength="4" 
                                inputmode="numeric"
                                pattern="\d{4}" 
                                title="Masukkan 4 digit angka" 
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="toggle-pin-btn" id="togglePin" title="Tampilkan/Sembunyikan PIN">
                                <i class="fa-solid fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <div class="pin-indicator">
                            <div class="pin-dot" id="dot1"></div>
                            <div class="pin-dot" id="dot2"></div>
                            <div class="pin-dot" id="dot3"></div>
                            <div class="pin-dot" id="dot4"></div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="btnSubmit">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Masuk ke Sistem</span>
                    </button>
                </form>

                <div class="form-footer">
                    <a href="{{ route('home') }}" class="back-link">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Halaman Utama
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pinInput = document.getElementById('pin');
            const togglePin = document.getElementById('togglePin');
            const toggleIcon = document.getElementById('toggleIcon');
            const dots = [
                document.getElementById('dot1'),
                document.getElementById('dot2'),
                document.getElementById('dot3'),
                document.getElementById('dot4')
            ];
            const loginForm = document.getElementById('loginForm');
            const btnSubmit = document.getElementById('btnSubmit');

            // Toggle show/hide PIN
            togglePin.addEventListener('click', function() {
                const isPassword = pinInput.getAttribute('type') === 'password';
                pinInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.classList.toggle('fa-eye', !isPassword);
                toggleIcon.classList.toggle('fa-eye-slash', isPassword);
            });

            // Enforce numeric only and update dot indicator
            pinInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                
                const valLength = this.value.length;
                dots.forEach((dot, idx) => {
                    if (idx < valLength) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            });

            // Prevent double submit with loading state
            loginForm.addEventListener('submit', function() {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i><span>Memproses...</span>';
            });
        });
    </script>
</body>
</html>

