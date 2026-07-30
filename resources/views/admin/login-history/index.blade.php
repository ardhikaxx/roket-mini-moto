@extends('layouts.admin')
@section('title', 'Riwayat Login')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Riwayat Login</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-right-to-bracket text-primary me-2"></i>Riwayat Login</h1>
            <p class="page-subtitle">Log aktivitas login & logout pengguna</p>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
    <div class="card-body p-4 bg-neutral-50">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Pengguna</label>
                <select name="user_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Aksi</label>
                <select name="action" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('action', 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <a href="{{ route('admin.login-history') }}" class="btn btn-light border fw-bold rounded-3 w-100"><i class="fa-solid fa-rotate-right"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th style="padding:16px 20px;">Pengguna</th>
                        <th style="padding:16px 20px;">Role</th>
                        <th style="padding:16px 20px;">Aksi</th>
                        <th style="padding:16px 20px;">IP Address</th>
                        <th style="padding:16px 20px;">Perangkat</th>
                        <th style="padding:16px 20px;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $h)
                    <tr>
                        <td style="padding:16px 20px;">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--neutral-100);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--text-secondary);flex-shrink:0;">
                                    {{ strtoupper(substr($h->user->name ?? '?', 0, 2)) }}
                                </div>
                                <span class="fw-bold text-dark" style="font-size:14px;">{{ $h->user->name ?? 'User Dihapus' }}</span>
                            </div>
                        </td>
                        <td style="padding:16px 20px;">
                            <span class="badge bg-light text-dark border px-3 py-1" style="font-size:11px;text-transform:capitalize;">{{ str_replace('_', ' ', $h->user->role ?? '-') }}</span>
                        </td>
                        <td style="padding:16px 20px;">
                            @if($h->action === 'login')
                                <span class="badge badge-success rounded-pill px-3 py-1"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</span>
                            @else
                                <span class="badge badge-danger rounded-pill px-3 py-1"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</span>
                            @endif
                        </td>
                        <td style="padding:16px 20px;">
                            <code style="font-size:12px;background:var(--neutral-100);padding:4px 10px;border-radius:6px;">{{ $h->ip_address ?? '-' }}</code>
                        </td>
                        <td style="padding:16px 20px;">
                            <div style="font-size:12px;color:var(--text-secondary);max-width:250px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $h->user_agent }}">
                                @if($h->user_agent)
                                    @php
                                        $ua = $h->user_agent;
                                        if (str_contains($ua, 'Windows')) echo '<i class="fa-brands fa-windows me-1"></i>';
                                        elseif (str_contains($ua, 'Android')) echo '<i class="fa-brands fa-android me-1"></i>';
                                        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) echo '<i class="fa-brands fa-apple me-1"></i>';
                                        elseif (str_contains($ua, 'Linux')) echo '<i class="fa-brands fa-linux me-1"></i>';
                                        else echo '<i class="fa-solid fa-globe me-1"></i>';
                                        echo ' ' . (preg_match('/(Chrome|Firefox|Safari|Edge|Opera)\/\d+/', $ua, $m) ? $m[1] : 'Browser Tidak Diketahui');
                                    @endphp
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td style="padding:16px 20px;">
                            <span style="font-size:13px;color:var(--text-secondary);">{{ $h->created_at->format('d M Y H:i') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat login.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $histories->links() }}
</div>
@endsection
