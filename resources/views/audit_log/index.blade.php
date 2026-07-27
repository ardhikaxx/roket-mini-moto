@extends('layouts.admin')
@section('title', 'Sistem Audit Log')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Sistem Audit Log</span>
@endsection

@section('content')
@php 
    // Menggunakan paginate atau get langsung, karena di file asal pake get() tapi sebaiknya kalau banyak pakai pagination
    // tapi karena di asal get(), kita ikuti get() dulu agar tidak merusak logika, namun dibungkus dengan DataTables
    $logs = \App\Models\AuditLog::with('user')->latest()->get(); 
@endphp

<div class="page-header stagger-1">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Sistem Audit Log</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;">Rekaman jejak seluruh aktivitas pengguna dan perubahan data di dalam sistem.</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <button class="btn btn-outline-secondary px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);" onclick="window.location.reload()">
                <i class="fa-solid fa-rotate-right me-2 text-muted"></i> Segarkan Data
            </button>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5 stagger-2" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Riwayat Aktivitas Terbaru
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-container border-0">
            <table class="table datatable align-middle table-hover" style="margin:0; min-width:800px;">
                <thead>
                    <tr>
                        <th style="width:180px;">Waktu & Tanggal</th>
                        <th style="width:250px;">Aktor / Pengguna</th>
                        <th style="width:150px;">Tindakan</th>
                        <th>Detail Catatan</th>
                        <th class="text-end" style="width:120px;">Modul Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark" style="font-size:13px;">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-muted font-monospace" style="font-size:11px;"><i class="fa-regular fa-clock me-1"></i>{{ $log->created_at->format('H:i:s') }} WIB</div>
                        </td>
                        <td>
                            @if($log->user)
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-initials-sm">{{ strtoupper(substr($log->user->name,0,2)) }}</div>
                                <div style="min-width: 0;">
                                    <div class="fw-bold text-dark text-truncate" style="font-size:13px;">{{ $log->user->name }}</div>
                                    <div class="text-muted text-truncate" style="font-size:11px;">{{ str_replace('_', ' ', $log->user->role) }}</div>
                                </div>
                            </div>
                            @else
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center text-muted fw-bold border border-light" style="width: 32px; height: 32px; border-radius: 8px; background: var(--neutral-100); font-size: 14px; flex-shrink: 0;">
                                    <i class="fa-solid fa-robot"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="fw-bold text-muted text-truncate" style="font-size:13px;">Sistem Otomatis</div>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $color = 'primary';
                                $icon = 'bolt';
                                if(str_contains(strtolower($log->action), 'login')) { $color = 'success'; $icon = 'right-to-bracket'; }
                                elseif(str_contains(strtolower($log->action), 'logout')) { $color = 'danger'; $icon = 'right-from-bracket'; }
                                elseif(str_contains(strtolower($log->action), 'create') || str_contains(strtolower($log->action), 'add')) { $color = 'info'; $icon = 'plus'; }
                                elseif(str_contains(strtolower($log->action), 'update') || str_contains(strtolower($log->action), 'edit')) { $color = 'warning'; $icon = 'pen'; }
                                elseif(str_contains(strtolower($log->action), 'delete') || str_contains(strtolower($log->action), 'remove')) { $color = 'danger'; $icon = 'trash'; }
                                elseif(str_contains(strtolower($log->action), 'approve')) { $color = 'success'; $icon = 'check-double'; }
                                elseif(str_contains(strtolower($log->action), 'reject')) { $color = 'danger'; $icon = 'xmark'; }
                            @endphp
                            <span class="badge bg-{{$color}}-50 text-{{$color}}-700 border border-{{$color}}-100 px-2 py-1" style="font-weight:700; letter-spacing:0.5px; font-size:10px;">
                                <i class="fa-solid fa-{{$icon}} me-1"></i> {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark" style="font-size:13px; line-height:1.5;">{{ $log->description }}</div>
                        </td>
                        <td class="text-end">
                            @if($log->model)
                                <span class="badge bg-neutral-100 text-neutral-600 border px-2 py-1" style="font-family:monospace; font-size:11px;">
                                    {{ class_basename($log->model) }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:12px;">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
