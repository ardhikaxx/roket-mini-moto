@extends('layouts.admin')
@section('title', 'Notifikasi')
@section('breadcrumb')
    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'karyawan.dashboard') }}">Dashboard</a>
    <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Notifikasi</span>
@endsection
@section('content')
@php $notifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->get(); $unread = $notifications->where('is_read', false)->count(); @endphp
<div class="page-header">
    <div class="page-header-row">
        <div><h1 class="page-title">Notifikasi</h1><p class="page-subtitle">{{ $unread }} belum dibaca</p></div>
        @if($unread > 0)
        <div class="page-actions">
            <form action="{{ route('notifications.read-all') }}" method="POST">@csrf
                <button type="submit" class="btn btn-ghost btn-sm"><i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca</button>
            </form>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @forelse($notifications as $n)
        <a href="{{ $n->url ?? '#' }}" class="notif-item {{ !$n->is_read ? 'unread' : '' }}" style="display:flex;text-decoration:none;color:inherit;" onclick="@if(!$n->is_read) fetch('{{ route('notifications.read', $n->id) }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}); @endif">
            <div class="notif-icon" style="background:{{ $n->type=='report_submitted' ? 'var(--info-100)' : ($n->type=='report_approved' ? 'var(--success-100)' : ($n->type=='report_rejected' ? 'var(--danger-100)' : 'var(--warning-100)')) }};color:{{ $n->type=='report_submitted' ? 'var(--info-600)' : ($n->type=='report_approved' ? 'var(--success-600)' : ($n->type=='report_rejected' ? 'var(--danger-600)' : 'var(--warning-600)')) }};">
                <i class="fa-solid {{ $n->type=='report_submitted' ? 'fa-file-circle-plus' : ($n->type=='report_approved' ? 'fa-check-circle' : ($n->type=='report_rejected' ? 'fa-times-circle' : 'fa-info-circle')) }}"></i>
            </div>
            <div class="notif-content">
                <div class="notif-title">{{ $n->title }}</div>
                <div class="notif-message" style="-webkit-line-clamp:3;">{{ $n->message }}</div>
                <div class="notif-time">{{ $n->created_at->diffForHumans() }}</div>
            </div>
        </a>
        @empty
        <div class="empty-state"><div class="empty-state-icon"><i class="fa-regular fa-bell"></i></div><div class="empty-state-title">Belum ada notifikasi</div><div class="empty-state-desc">Notifikasi akan muncul ketika ada aktivitas yang memerlukan perhatian Anda.</div></div>
        @endforelse
    </div>
</div>
@endsection
