@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')
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
<div class="card stagger-1 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Penugasan Toko</th>
                        <th>Status</th>
                        <th class="cell-action text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr class="align-middle">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-600)); font-size: 1rem;">
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
                        <td><span class="badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="cell-action text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-icon-sm rounded-circle" data-bs-toggle="dropdown" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="dropdown-item fw-semibold"><i class="fa-regular fa-eye"></i> Detail</a>
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="dropdown-item fw-semibold"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" id="form-del-{{ $u->id }}">@csrf @method('DELETE')
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
function confirmDelete(id, name) {
    Swal.fire({title:'Nonaktifkan Akun?',text:'Akun "'+name+'" tidak akan bisa login.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Ya, Nonaktifkan',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); });
}
</script>
@endsection