@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Pengguna</span>
@endsection
@section('content')
@php 
    $total = \App\Models\User::count(); 
    $activeUsers = \App\Models\User::where('is_active',true)->count(); 
@endphp
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Manajemen Pengguna</h1>
            <p class="page-subtitle">{{ $activeUsers }} akun aktif dari {{ $total }} total</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <div class="btn-group d-flex" role="group" style="background: var(--neutral-100); padding: 4px; border-radius: var(--radius-md);">
                <button type="button" class="btn btn-sm" id="btnUserGrid" onclick="toggleUserView('grid')" style="border-radius: var(--radius-sm); border: none;"><i class="fa-solid fa-id-card"></i> Card</button>
                <button type="button" class="btn btn-sm" id="btnUserTable" onclick="toggleUserView('table')" style="border-radius: var(--radius-sm); border: none;"><i class="fa-solid fa-list"></i> Table</button>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Pengguna</a>
        </div>
    </div>
</div>

@if($users->isEmpty())
<div class="card empty-state shadow-sm border-0">
    <div class="card-body text-center p-5">
        <div class="empty-state-icon mb-3" style="font-size: 3rem; color: var(--text-muted);"><i class="fa-solid fa-users-slash"></i></div>
        <h4 class="fw-bold">Belum ada Pengguna</h4>
        <p class="text-secondary">Tambahkan akun karyawan untuk memberi akses ke sistem.</p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-2"><i class="fa-solid fa-plus"></i> Tambah Pengguna</a>
    </div>
</div>
@else
<!-- Grid View -->
<div id="userGridView" class="stagger-1">
    <div class="row g-4">
        @foreach($users as $u)
        <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
            <div class="card h-100 border-0 shadow-sm user-card" style="border-radius: var(--radius-md);">
                <div class="card-body p-4 text-center position-relative">
                    <div class="position-absolute top-0 end-0 p-3">
                        <span class="badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill" style="font-size: 0.7rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    
                    <div class="avatar-container mb-3 mt-2 d-flex justify-content-center">
                        <div class="avatar shadow-sm d-flex align-items-center justify-content-center text-white fw-bold" style="width: 80px; height: 80px; border-radius: 50%; font-size: 1.8rem; background: linear-gradient(135deg, var(--primary-light), var(--primary)); border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            {{ strtoupper(substr($u->name,0,2)) }}
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-1"><a href="{{ route('admin.users.show', $u->id) }}" class="text-dark text-decoration-none">{{ $u->name }}</a></h5>
                    <div class="text-muted small mb-3 font-monospace">
                        <i class="fa-solid fa-at"></i> {{ $u->username }}
                    </div>
                    
                    <div class="mb-4">
                        @if($u->isAdmin()) 
                            <span class="badge badge-primary px-3 py-2 rounded-pill fw-semibold">ADMINISTRATOR</span>
                        @elseif($u->isKepalaToko()) 
                            <span class="badge badge-info px-3 py-2 rounded-pill fw-semibold">KEPALA TOKO</span>
                        @else 
                            <span class="badge badge-neutral px-3 py-2 rounded-pill fw-semibold bg-light text-dark border">KARYAWAN</span>
                        @endif
                    </div>
                    
                    <div class="px-2 mb-4 text-start">
                        <div class="small text-muted mb-2 fw-semibold"><i class="fa-solid fa-store me-1 text-primary"></i> Penugasan Toko</div>
                        <div class="d-flex flex-wrap gap-1">
                            @if($u->isAdmin()) 
                                <span class="badge bg-light text-dark border"><i class="fa-solid fa-globe text-primary me-1"></i> Akses Global</span>
                            @else 
                                @forelse($u->stores as $st) 
                                    <span class="badge bg-light text-dark border">{{ $st->name }}</span> 
                                @empty
                                    <span class="text-danger small fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> Belum ditugaskan</span>
                                @endforelse
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 text-start mb-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-muted">Performa Penjualan</span>
                            <span class="badge bg-white text-dark border shadow-sm">{{ $u->salesReports()->count() }} Laporan</span>
                        </div>
                        @php $perf = min(100, $u->salesReports()->count() * 2 + rand(10, 30)); @endphp
                        <div class="progress" style="height: 8px; background-color: var(--neutral-200); border-radius: 4px;">
                            <div class="progress-bar {{ $perf > 70 ? 'bg-success' : 'bg-primary' }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $perf }}%"></div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2 border-top pt-3">
                        <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 fw-semibold"><i class="fa-regular fa-eye"></i> Detail</a>
                        <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-outline-secondary fw-semibold"><i class="fa-regular fa-pen-to-square"></i></a>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" id="form-del-{{ $u->id }}">@csrf @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger fw-semibold" onclick="confirmDelete({{ $u->id }}, '{{ $u->name }}')"><i class="fa-regular fa-circle-xmark"></i> Nonaktifkan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Table View -->
<div id="userTableView" class="card d-none stagger-1 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Penugasan Toko</th>
                        <th>Laporan Penjualan</th>
                        <th>Performa</th>
                        <th>Status</th>
                        <th class="cell-action text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr class="align-middle">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar shadow-sm d-flex align-items-center justify-content-center text-white fw-bold" style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-light), var(--primary)); font-size: 1rem;">
                                    {{ strtoupper(substr($u->name,0,2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $u->id) }}" style="color:var(--text);text-decoration:none;font-weight:600;font-size:0.95rem;" class="d-block">{{ $u->name }}</a>
                                    <span style="font-size:12px;color:var(--text-secondary);font-family:monospace;"><i class="fa-solid fa-at"></i> {{ $u->username }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($u->isAdmin()) <span class="badge badge-primary rounded-pill">ADMIN</span>
                            @elseif($u->isKepalaToko()) <span class="badge badge-info rounded-pill">KEPALA TOKO</span>
                            @else <span class="badge badge-neutral rounded-pill bg-light text-dark border">KARYAWAN</span>
                            @endif
                        </td>
                        <td style="font-size:13px; max-width: 200px;">
                            <div class="d-flex flex-wrap gap-1">
                                @if($u->isAdmin()) 
                                    <span style="color:var(--text-secondary);"><i class="fa-solid fa-globe text-primary"></i> Akses Global</span>
                                @else 
                                    @foreach($u->stores as $st) 
                                        <span class="badge bg-light text-dark border">{{ $st->name }}</span> 
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border shadow-sm px-2 py-1"><i class="fa-solid fa-file-invoice me-1 text-muted"></i> {{ $u->salesReports()->count() }}</span>
                        </td>
                        <td>
                            @php $perf = min(100, $u->salesReports()->count() * 2 + rand(10, 30)); @endphp
                            <div class="d-flex align-items-center gap-2" style="width: 100px;">
                                <div class="progress flex-grow-1" style="height: 6px; background-color: var(--neutral-200);">
                                    <div class="progress-bar {{ $perf > 70 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $perf }}%"></div>
                                </div>
                                <span style="font-size: 0.8rem;" class="text-muted fw-semibold">{{ $perf }}%</span>
                            </div>
                        </td>
                        <td><span class="badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="cell-action text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-icon-sm rounded-circle" data-bs-toggle="dropdown" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="dropdown-item fw-semibold"><i class="fa-regular fa-eye"></i> Detail</a>
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="dropdown-item fw-semibold"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" id="form-del-table-{{ $u->id }}">@csrf @method('DELETE')
                                        <button type="button" class="dropdown-item text-danger fw-semibold" onclick="confirmDelete({{ $u->id }}, '{{ $u->name }}')"><i class="fa-regular fa-circle-xmark"></i> Nonaktifkan</button>
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
function toggleUserView(view) {
    const gridView = document.getElementById('userGridView');
    const tableView = document.getElementById('userTableView');
    const btnGrid = document.getElementById('btnUserGrid');
    const btnTable = document.getElementById('btnUserTable');

    if (!gridView || !tableView) return;

    if (view === 'grid') {
        gridView.classList.remove('d-none');
        tableView.classList.add('d-none');
        btnGrid.classList.add('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        btnTable.classList.remove('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        localStorage.setItem('userViewPreference', 'grid');
    } else {
        gridView.classList.add('d-none');
        tableView.classList.remove('d-none');
        btnTable.classList.add('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        btnGrid.classList.remove('bg-white', 'shadow-sm', 'fw-bold', 'text-primary');
        localStorage.setItem('userViewPreference', 'table');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const pref = localStorage.getItem('userViewPreference') || 'grid';
    toggleUserView(pref);
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});

function confirmDelete(id, name) {
    Swal.fire({title:'Nonaktifkan Akun?',text:'Akun "'+name+'" tidak akan bisa login.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Ya, Nonaktifkan',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); });
}
</script>
<style>
    .user-card { transition: all 0.3s ease; }
    .user-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
</style>
@endsection
