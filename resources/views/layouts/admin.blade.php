<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Roket Mini Moto - Retail Management System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/design-system.css') }}">
    <style>.input-rupiah { padding-left: 12px; }</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body>
    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-motorcycle"></i></div>
            <div class="brand-text">
                <span class="brand-name">Roket Mini Moto</span>
                <span class="brand-sub">Retail System</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            @php
                $user = auth()->user();
                $dashboardRoute = $user->isAdmin() ? 'admin.dashboard' : ($user->isKepalaToko() ? 'kepalatoko.dashboard' : 'karyawan.dashboard');
            @endphp

            <div class="nav-group-label">Overview</div>
            <a href="{{ route($dashboardRoute) }}" class="nav-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span>
                <span class="nav-text">Dashboard</span>
            </a>

            @if($user->isAdmin() || $user->isKepalaToko())
            <div class="nav-group-label">Operasional</div>
            <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                <span class="nav-text">Laporan Penjualan</span>
            </a>
            @endif

            @if($user->isAdmin())
            <div class="nav-group-label">Master Data</div>
            <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-box-open"></i></span>
                <span class="nav-text">Manajemen Produk</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-tags"></i></span>
                <span class="nav-text">Kategori</span>
            </a>
            <a href="{{ route('admin.stores.index') }}" class="nav-item {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-store"></i></span>
                <span class="nav-text">Toko & Cabang</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                <span class="nav-text">Pengguna & Karyawan</span>
            </a>
            <div class="nav-group-label">Analitik</div>
            <a href="{{ route('admin.omzet') }}" class="nav-item {{ request()->routeIs('admin.omzet') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                <span class="nav-text">Analitik Omzet</span>
            </a>
            <div class="nav-group-label">Inventori & Target</div>
            <a href="{{ route('admin.stock.index') }}" class="nav-item {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-warehouse"></i></span>
                <span class="nav-text">Manajemen Stok</span>
            </a>
            <a href="{{ route('admin.profit-loss') }}" class="nav-item {{ request()->routeIs('admin.profit-loss') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-coins"></i></span>
                <span class="nav-text">Laba Rugi</span>
            </a>
            <div class="nav-group-label">Sistem</div>
            <a href="{{ route('admin.login-history') }}" class="nav-item {{ request()->routeIs('admin.login-history') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-right-to-bracket"></i></span>
                <span class="nav-text">Riwayat Login</span>
            </a>
            <a href="{{ route('admin.audit-log') }}" class="nav-item {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <span class="nav-text">Audit Log</span>
            </a>
            @endif

            @if($user->isKaryawan())
            <div class="nav-group-label">Aktivitas Harian</div>
            <a href="{{ route('karyawan.reports.create') }}" class="nav-item {{ request()->routeIs('karyawan.reports.create') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-plus-circle"></i></span>
                <span class="nav-text">Buat Laporan</span>
            </a>
            <a href="{{ route('karyawan.reports.index') }}" class="nav-item {{ request()->routeIs('karyawan.reports.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                <span class="nav-text">Histori Laporan Saya</span>
            </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user" onclick="document.getElementById('userDropdownSidebar').classList.toggle('show')">
                <div class="user-avatar">
                    @if($user->photo)
                        <img src="{{ asset('storage/'.$user->photo) }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    @endif
                </div>
                <div class="user-info">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-role">{{ str_replace('_', ' ', $user->role) }}</div>
                </div>
            </div>
            <div id="userDropdownSidebar" class="dropdown-menu w-100" style="position:relative; top:auto; margin-top:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);">
                <a href="{{ route('profile') }}" class="dropdown-item" style="color:#e2e8f0;"><i class="fa-solid fa-user-circle"></i> Profile Setting</a>
                <div class="dropdown-divider" style="background:rgba(255,255,255,0.1);"></div>
                <button class="dropdown-item" style="color:#ef4444;" onclick="confirmLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </div>
        </div>
    </aside>

    {{-- Navbar --}}
    <header class="navbar-top" id="navbarTop">
        <button class="navbar-toggle" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>

        <div class="navbar-search d-none d-md-flex">
            <i class="fa-solid fa-search"></i>
            <input type="text" placeholder="Cari laporan, produk, atau data...">
        </div>

        <div class="navbar-spacer"></div>

        <div class="navbar-actions">
            @php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
            <div class="dropdown" id="notifDropdownContainer">
                <button class="navbar-action-btn" onclick="document.getElementById('notifMenu').classList.toggle('show')">
                    <i class="fa-regular fa-bell"></i>
                    @if($unreadCount > 0)
                        <span class="badge-pill">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu" id="notifMenu" style="width: 320px; right: -60px;">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <h6 class="mb-0 fw-bold">Notifikasi</h6>
                        <a href="{{ route('notifications') }}" class="text-primary text-decoration-none" style="font-size:12px;">Lihat Semua</a>
                    </div>
                    <div style="max-height:300px; overflow-y:auto;">
                        @php $recentNotifs = \App\Models\Notification::where('user_id', auth()->id())->latest()->limit(4)->get(); @endphp
                        @forelse($recentNotifs as $notif)
                            <a href="{{ $notif->url ?? route('notifications') }}" class="d-flex gap-3 p-3 text-decoration-none border-bottom" style="background:{{ !$notif->is_read ? 'var(--primary-50)' : 'transparent' }}">
                                <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--{{ $notif->type == 'report_submitted' ? 'info' : ($notif->type == 'report_approved' ? 'success' : ($notif->type == 'report_rejected' ? 'danger' : 'primary')) }}-100);color:var(--{{ $notif->type == 'report_submitted' ? 'info' : ($notif->type == 'report_approved' ? 'success' : ($notif->type == 'report_rejected' ? 'danger' : 'primary')) }}-600);flex-shrink:0;">
                                    <i class="fa-solid {{ $notif->type == 'report_submitted' ? 'fa-file-invoice' : ($notif->type == 'report_approved' ? 'fa-check' : ($notif->type == 'report_rejected' ? 'fa-xmark' : 'fa-bell')) }}"></i>
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:2px;">{{ $notif->title }}</div>
                                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;line-height:1.4;">{{ $notif->message }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center p-4 text-muted" style="font-size:13px;">Belum ada notifikasi</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="navbar-user-dropdown" onclick="document.getElementById('navUserMenu').classList.toggle('show')">
                <div class="user-avatar-sm">
                    @if($user->photo)
                        <img src="{{ asset('storage/'.$user->photo) }}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                    @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    @endif
                </div>
                <div class="user-name-sm d-none d-md-block">{{ explode(' ', trim($user->name))[0] }}</div>
                <i class="fa-solid fa-chevron-down dropdown-icon d-none d-md-block"></i>
                
                <div class="dropdown-menu" id="navUserMenu">
                    <div class="px-3 py-2 border-bottom mb-1">
                        <div class="fw-bold" style="font-size:14px;color:var(--text);">{{ $user->name }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);">{{ str_replace('_', ' ', $user->role) }}</div>
                    </div>
                    <a href="{{ route('profile') }}" class="dropdown-item"><i class="fa-regular fa-user"></i> Profile Setting</a>
                    <a href="{{ route('profile.change-pin') }}" class="dropdown-item"><i class="fa-solid fa-lock"></i> Ganti PIN</a>
                    <div class="dropdown-divider"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                    <button class="dropdown-item text-danger" onclick="confirmLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</button>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Bottom Navigation --}}
    <nav class="bottom-nav" id="bottomNav">
        <a href="{{ route($dashboardRoute) }}" class="bottom-nav-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
            <span class="bottom-nav-icon"><i class="fa-solid fa-chart-pie"></i></span>
            <span class="bottom-nav-label">Dashboard</span>
        </a>

        @if($user->isAdmin() || $user->isKepalaToko())
        <a href="{{ route('admin.reports.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            <span class="bottom-nav-label">Laporan</span>
        </a>
        @elseif($user->isKaryawan())
        <a href="{{ route('karyawan.reports.create') }}" class="bottom-nav-item {{ request()->routeIs('karyawan.reports.create') ? 'active' : '' }}">
            <span class="bottom-nav-icon"><i class="fa-solid fa-plus-circle"></i></span>
            <span class="bottom-nav-label">Buat</span>
        </a>
        @endif

        @if($user->isAdmin())
        <a href="{{ route('admin.products.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon"><i class="fa-solid fa-box-open"></i></span>
            <span class="bottom-nav-label">Produk</span>
        </a>
        @elseif($user->isKaryawan())
        <a href="{{ route('karyawan.reports.index') }}" class="bottom-nav-item {{ request()->routeIs('karyawan.reports.index') ? 'active' : '' }}">
            <span class="bottom-nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
            <span class="bottom-nav-label">Histori</span>
        </a>
        @endif

        <a href="{{ route('notifications') }}" class="bottom-nav-item {{ request()->routeIs('notifications') ? 'active' : '' }}">
            <span class="bottom-nav-icon"><i class="fa-regular fa-bell"></i></span>
            <span class="bottom-nav-label">Notif</span>
        </a>

        <a href="{{ route('profile') }}" class="bottom-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
            <span class="bottom-nav-icon"><i class="fa-regular fa-user"></i></span>
            <span class="bottom-nav-label">Profil</span>
        </a>
    </nav>

    {{-- Main Content --}}
    <main class="main-content page-transition" id="mainContent">
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        icon:'success', title:'Berhasil', text:'{{ session("success") }}', 
                        timer:3000, showConfirmButton:false, padding:'24px', 
                        customClass:{popup:'rounded-4 shadow-lg'}
                    });
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        icon:'error', title:'Oops...', text:'{{ session("error") }}', 
                        confirmButtonColor:'#4f46e5', customClass:{popup:'rounded-4 shadow-lg'}
                    });
                });
            </script>
        @endif

        @yield('content')
    </main>

    <div id="sidebarOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:1039;" onclick="toggleSidebar()"></div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            const navbar = document.getElementById('navbarTop');
            const overlay = document.getElementById('sidebarOverlay');
            if (window.innerWidth <= 991) {
                sidebar.classList.toggle('show');
                overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            } else {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('sidebar-collapsed');
                navbar.classList.toggle('sidebar-collapsed');
            }
        }

        // Handle dropdowns closing when clicking outside
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                const parent = m.closest('.navbar-user-dropdown, .dropdown');
                if (!m.contains(e.target) && (!parent || !parent.contains(e.target))) {
                    m.classList.remove('show');
                }
            });
        });

        function confirmLogout() {
            Swal.fire({
                title: 'Logout System?',
                text: 'Sesi Anda akan diakhiri.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then((r) => { if (r.isConfirmed) { document.getElementById('logout-form').submit(); }});
        }

        function animateCountUp() {
            document.querySelectorAll('[data-count]').forEach(el => {
                const target = parseFloat(el.dataset.count);
                if (isNaN(target)) return;
                const duration = 2000;
                const start = performance.now();
                const isCurrency = el.textContent.includes('Rp');
                function update(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const current = Math.floor(progress * target);
                    if (isCurrency) {
                        el.textContent = 'Rp ' + current.toLocaleString('id-ID');
                    } else {
                        el.textContent = current.toLocaleString('id-ID');
                    }
                    if (progress < 1) requestAnimationFrame(update);
                    else if (isCurrency) el.textContent = 'Rp ' + target.toLocaleString('id-ID');
                    else el.textContent = target.toLocaleString('id-ID');
                }
                requestAnimationFrame(update);
            });
        }
        document.addEventListener('DOMContentLoaded', animateCountUp);

        // DataTables init with premium defaults
        $(document).ready(function() {
            if ($('.datatable').length) {
                $('.datatable').each(function() {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                            pageLength: 25,
                            lengthMenu: [10, 25, 50, 100],
                            order: [],
                            autoWidth: false,
                            scrollX: true,
                            scrollCollapse: true,
                            drawCallback: function() {
                                $(this).closest('.dataTables_wrapper').find('.dataTables_filter input').attr('placeholder', 'Cari data...');
                            },
                            initComplete: function() {
                                const wrapper = $(this).closest('.dataTables_wrapper');
                                wrapper.find('.dataTables_filter input').addClass('form-control-sm');
                                wrapper.find('.dataTables_length select').addClass('form-select-sm');
                                wrapper.addClass('dt-premium');
                            }
                        });
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.input-rupiah').forEach(function(input) {
                function formatRupiah(val) {
                    var num = val.replace(/[^0-9]/g, '');
                    if (num === '') return '';
                    return parseInt(num).toLocaleString('id-ID');
                }
                if (input.value) input.value = formatRupiah(input.value);
                input.addEventListener('input', function(e) {
                    var caret = this.selectionStart;
                    var raw = this.value.replace(/[^0-9]/g, '');
                    var before = this.value.length;
                    this.value = formatRupiah(raw);
                    var after = this.value.length;
                    if (caret < before) {
                        caret = Math.max(1, caret - (before - after));
                        this.setSelectionRange(caret, caret);
                    }
                });
                var form = input.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        input.value = input.value.replace(/[^0-9]/g, '');
                    });
                }
            });
        });
    </script>
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
