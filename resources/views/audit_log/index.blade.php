@extends('layouts.admin')
@section('title', 'Audit Log')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Audit Log</span>
@endsection
@section('content')
@php $logs = \App\Models\AuditLog::with('user')->latest()->get(); @endphp
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Audit Log</h1><p class="page-subtitle">Riwayat aktivitas sistem</p></div></div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Deskripsi</th><th>Modul</th></tr></thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td style="font-size:13px;color:var(--text-secondary);white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm" style="width:28px;height:28px;font-size:10px;">{{ strtoupper(substr($log->user->name ?? '?',0,2)) }}</div>
                                <span>{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td><span class="badge badge-neutral">{{ $log->action }}</span></td>
                        <td style="font-size:13px;">{{ $log->description }}</td>
                        <td style="font-size:13px;color:var(--text-secondary);">{{ $log->model ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
