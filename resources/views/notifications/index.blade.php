@extends('layouts.admin')
@section('title', 'Notifikasi & Pemberitahuan')
@section('breadcrumb')
    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'karyawan.dashboard') }}">Dashboard</a>
    <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Notifikasi Sistem</span>
@endsection

@section('content')
@php 
    $notifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->get(); 
    $unread = $notifications->where('is_read', false)->count(); 
@endphp

<div class="page-header stagger-1">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-bell text-primary me-2"></i>Pusat Notifikasi</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;">Kelola pemberitahuan aktivitas, laporan, dan peringatan sistem.</p>
        </div>
        <div class="page-actions d-flex gap-2 align-items-center">
            @if($unread > 0)
            <div class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold" style="font-size:12px; letter-spacing:0.5px;">
                {{ $unread }} Pesan Baru
            </div>
            <form action="{{ route('notifications.read-all') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-secondary px-3 py-2" style="font-weight:600;border:1px solid var(--border-light); font-size:13px;">
                    <i class="fa-solid fa-check-double me-2 text-primary"></i> Tandai Semua Terbaca
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5 stagger-2" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 border-bottom border-light d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
            <i class="fa-solid fa-inbox text-primary me-2"></i> Kotak Masuk
        </h5>
        @if($notifications->count() > 0)
        <span class="text-muted" style="font-size:12px;">Total: {{ $notifications->count() }} notifikasi</span>
        @endif
    </div>
    
    <div class="card-body p-0">
        <div class="list-group list-group-flush" style="border-radius:0;">
            @forelse($notifications as $n)
                @php
                    $bgClass = 'bg-primary-50';
                    $iconClass = 'text-primary';
                    $icon = 'bell';
                    
                    if($n->type == 'report_submitted') { $bgClass = 'bg-info-50'; $iconClass = 'text-info'; $icon = 'file-invoice'; }
                    elseif($n->type == 'report_approved') { $bgClass = 'bg-success-50'; $iconClass = 'text-success'; $icon = 'circle-check'; }
                    elseif($n->type == 'report_rejected') { $bgClass = 'bg-danger-50'; $iconClass = 'text-danger'; $icon = 'circle-xmark'; }
                    elseif($n->type == 'system_alert') { $bgClass = 'bg-warning-50'; $iconClass = 'text-warning'; $icon = 'triangle-exclamation'; }
                @endphp
                
                <a href="{{ $n->url ?? '#' }}" 
                   class="list-group-item list-group-item-action p-4 border-bottom {{ !$n->is_read ? 'bg-primary-50' : 'bg-white' }}" 
                   style="transition:all 0.2s; border-color:var(--border-light);"
                   onclick="@if(!$n->is_read) fetch('{{ route('notifications.read', $n->id) }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}); @endif">
                    
                    <div class="d-flex gap-4 align-items-start">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 {{ $bgClass }} {{ $iconClass }} rounded-circle shadow-sm" style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="fa-solid fa-{{ $icon }}"></i>
                        </div>
                        
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold {{ !$n->is_read ? 'text-primary-800' : 'text-dark' }}" style="font-size:15px;">
                                    {{ $n->title }}
                                    @if(!$n->is_read)
                                        <span class="badge bg-danger ms-2 rounded-pill" style="font-size:10px;">Baru</span>
                                    @endif
                                </h6>
                                <span class="text-muted d-flex align-items-center" style="font-size:11px; white-space:nowrap;">
                                    <i class="fa-regular fa-clock me-1"></i> {{ $n->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="mb-0 {{ !$n->is_read ? 'text-dark fw-semibold' : 'text-muted' }}" style="font-size:13px; line-height:1.6;">
                                {{ $n->message }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state py-5 text-center">
                    <div style="width:80px;height:80px;border-radius:50%;background:var(--neutral-100);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px;">
                        <i class="fa-regular fa-bell-slash"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Belum ada notifikasi</h5>
                    <p class="text-muted mx-auto" style="max-width:400px; font-size:14px;">Semua pemberitahuan sistem, persetujuan laporan, dan peringatan akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.list-group-item-action:hover { background-color: var(--neutral-50) !important; cursor: pointer; }
.bg-primary-50.list-group-item-action:hover { background-color: var(--primary-100) !important; }
</style>
@endsection
