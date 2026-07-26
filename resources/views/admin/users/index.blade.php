@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')

@section('content')
@php 
    $total = \App\Models\User::count(); 
    $activeUsers = \App\Models\User::where('is_active', true)->count(); 
    $inactiveUsers = $total - $activeUsers;
@endphp

<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-users text-primary me-2"></i>Pengelolaan Akses Pengguna</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;">Atur hak akses karyawan, admin, dan kepala toko</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary px-4 py-2" style="font-weight:600; box-shadow:0 4px 12px rgba(230,57,70,0.25);">
                <i class="fa-solid fa-user-plus me-2"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-4 mb-4 stagger-1">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); background:linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:20px;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Total Akun</p>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size:24px;">{{ $total }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:var(--success-50);color:var(--success);display:flex;align-items:center;justify-content:center;font-size:20px;">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Akun Aktif</p>
                    <h3 class="fw-bold mb-0 text-success" style="font-size:24px;">{{ $activeUsers }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:var(--danger-50);color:var(--danger);display:flex;align-items:center;justify-content:center;font-size:20px;">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Akses Diblokir</p>
                    <h3 class="fw-bold mb-0 text-danger" style="font-size:24px;">{{ $inactiveUsers }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

@if($users->isEmpty())
<div class="card empty-state shadow-sm border-0" style="border-radius:var(--radius-lg);">
    <div class="card-body text-center py-5">
        <div class="empty-state-icon mb-3 mx-auto" style="width:80px;height:80px;background:var(--neutral-100);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size: 2.5rem; color: var(--text-muted);">
            <i class="fa-solid fa-users-slash"></i>
        </div>
        <h4 class="fw-bold text-dark">Belum Ada Pengguna</h4>
        <p class="text-muted mb-4" style="font-size:14px; max-width:400px; margin:0 auto;">Sistem belum memiliki akun karyawan. Tambahkan pengguna baru agar mereka dapat mengakses sistem sesuai peran mereka.</p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary px-4 py-2" style="font-weight:600;"><i class="fa-solid fa-plus me-2"></i> Tambah Pengguna Sekarang</a>
    </div>
</div>
@else
<div class="card stagger-1 shadow-sm border-0 mb-5" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0" style="font-size:16px;">Daftar Seluruh Pengguna</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover align-middle" style="margin:0; min-width:800px;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:300px;">Profil Pengguna</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:180px;">Peran Akses</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:200px;">Lokasi Tugas</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:120px;">Status</th>
                        <th class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr style="transition: all 0.2s;">
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-600)); font-size: 1.1rem; flex-shrink: 0;">
                                    {{ strtoupper(substr($u->name,0,2)) }}
                                </div>
                                <div style="min-width: 0;">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="fw-bold text-primary text-decoration-none d-block text-truncate mb-1" style="font-size:15px;">{{ $u->name }}</a>
                                    <div class="text-muted text-truncate font-monospace" style="font-size:12px;">
                                        <i class="fa-solid fa-at me-1"></i>{{ $u->username }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            @if($u->isAdmin()) 
                                <span class="badge bg-danger-50 text-danger-700 border border-danger-100 px-3 py-2" style="font-size:11px; font-weight:700; letter-spacing:0.5px;">ADMINISTRATOR</span>
                            @elseif($u->isKepalaToko()) 
                                <span class="badge bg-primary-50 text-primary-700 border border-primary-100 px-3 py-2" style="font-size:11px; font-weight:700; letter-spacing:0.5px;">KEPALA TOKO</span>
                            @else 
                                <span class="badge bg-light text-dark border px-3 py-2" style="font-size:11px; font-weight:700; letter-spacing:0.5px;">KARYAWAN</span>
                            @endif
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex flex-wrap gap-1">
                                @if($u->isAdmin()) 
                                    <span class="text-primary fw-semibold" style="font-size:13px;"><i class="fa-solid fa-earth-asia me-1"></i> Seluruh Cabang</span>
                                @else 
                                    @if($u->stores->isEmpty())
                                        <span class="text-muted" style="font-size:13px; font-style:italic;">Belum ada penugasan</span>
                                    @else
                                        @foreach($u->stores as $st) 
                                            <span class="badge bg-neutral-100 text-dark border border-light px-2 py-1" style="font-size:12px; font-weight:600;"><i class="fa-solid fa-store me-1 text-secondary"></i> {{ $st->name }}</span> 
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            @if($u->is_active)
                                <span class="badge badge-success rounded-pill px-2 py-1" style="font-size:11px; font-weight:600;"><i class="fa-solid fa-check-circle me-1"></i> Aktif</span>
                            @else
                                <span class="badge badge-danger rounded-pill px-2 py-1" style="font-size:11px; font-weight:600;"><i class="fa-solid fa-circle-xmark me-1"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-3" style="border:1px solid var(--border-light);background:white;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;" onclick="this.nextElementSibling.classList.toggle('show')">
                                    <i class="fa-solid fa-ellipsis-vertical text-secondary"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="min-width: 160px;">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-eye me-2 w-15px text-center"></i> Profil Detail</a>
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-pen-to-square me-2 w-15px text-center"></i> Edit Akses</a>
                                    <div class="dropdown-divider my-1"></div>
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="m-0" id="form-del-{{ $u->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="dropdown-item py-2 fw-semibold text-danger" style="font-size:14px;" onclick="confirmDelete({{ $u->id }}, '{{ addslashes($u->name) }}')">
                                            <i class="fa-solid fa-user-lock me-2 w-15px text-center"></i> Blokir Akun
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<script>
// Menutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Blokir Akses?',
        html: `Akun atas nama <strong>${name}</strong> akan diblokir.<br>Pengguna ini tidak akan bisa login ke dalam sistem lagi.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-ban me-1"></i> Ya, Blokir',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); 
    });
}
</script>
@endsection