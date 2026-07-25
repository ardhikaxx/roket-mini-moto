@extends('layouts.admin')
@section('title', $user->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.users.index') }}">Pengguna</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $user->name }}</span>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3" style="font-size:28px;">
                    @if($user->photo)<img src="{{ asset('storage/'.$user->photo) }}">@else{{ strtoupper(substr($user->name,0,2)) }}@endif
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <div style="font-size:13px;color:var(--text-secondary);">@ {{ $user->username }}</div>
                <div class="mt-2">
                    @if($user->isAdmin()) <span class="badge badge-primary">ADMIN</span>
                    @elseif($user->isKepalaToko()) <span class="badge badge-info">KEPALA TOKO</span>
                    @else <span class="badge badge-neutral">KARYAWAN</span>
                    @endif
                    <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }} ms-1">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div style="font-size:13px;color:var(--text-secondary);margin-top:12px;">
                    @if($user->phone)<i class="fa-solid fa-phone me-1"></i>{{ $user->phone }}<br>@endif
                    @if($user->last_login_at)Terakhir login: {{ $user->last_login_at->diffForHumans() }}@endif
                </div>
                <div class="d-flex gap-2 mt-3 flex-wrap justify-content-center">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                    <button class="btn btn-ghost btn-sm" onclick="showResetPin()"><i class="fa-solid fa-key"></i> Reset PIN</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row mb-4">
            <div class="col-4"><div class="stat-card" style="padding:14px;cursor:default;"><div class="stat-label">Disetujui</div><div class="stat-value" style="font-size:20px;color:var(--success);">{{ $approvedCount }}</div></div></div>
            <div class="col-4"><div class="stat-card" style="padding:14px;cursor:default;"><div class="stat-label">Ditolak</div><div class="stat-value" style="font-size:20px;color:var(--danger);">{{ $rejectedCount }}</div></div></div>
            <div class="col-4"><div class="stat-card" style="padding:14px;cursor:default;"><div class="stat-label">Diproses</div><div class="stat-value" style="font-size:20px;color:var(--warning);">{{ $pendingCount }}</div></div></div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="fw-bold mb-0">Informasi</h5></div>
            <div class="card-body">
                <div class="row" style="font-size:14px;">
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">Total Omzet Disetujui</span><br><span class="fw-bold" style="font-size:18px;">Rp {{ number_format($totalApprovedAmount,0,',','.') }}</span></div>
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">Total Laporan</span><br><span class="fw-bold">{{ $user->salesReports->count() }}</span></div>
                </div>
                @if(!$user->isAdmin())
                <div class="mt-3"><span style="color:var(--text-secondary);font-size:13px;">Penugasan Toko:</span><br>
                    @forelse($user->stores as $s) <span class="badge badge-neutral">{{ $s->name }}</span> @empty <span style="color:var(--text-muted);font-size:13px;">Belum ditugaskan</span> @endforelse
                </div>
                @endif
            </div>
        </div>

        @if($user->salesReports->count() > 0)
        <div class="card">
            <div class="card-header"><h5 class="fw-bold mb-0">Laporan Terbaru</h5></div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table" style="margin:0;">
                        <thead><tr><th>Tanggal</th><th>Toko</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($user->salesReports()->latest()->take(10)->get() as $r)
                            <tr><td style="font-size:13px;">{{ $r->created_at->format('d/m/Y H:i') }}</td><td>{{ $r->store->name ?? '-' }}</td><td>Rp {{ number_format($r->total_amount,0,',','.') }}</td><td><span class="badge {{ $r->status=='disetujui'?'badge-success':($r->status=='ditolak'?'badge-danger':'badge-warning') }}">{{ strtoupper($r->status) }}</span></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<form id="resetPinForm" action="{{ route('admin.users.reset-pin', $user->id) }}" method="POST" style="display:none;">@csrf</form>
<script>
function showResetPin() {
    Swal.fire({
        title:'Reset PIN Pengguna',
        html:'<div class="form-group"><label class="form-label">PIN Baru (4 digit)</label><input type="password" id="newPin" class="form-control" maxlength="4" pattern="[0-9]{4}"></div>',
        showCancelButton:true,confirmButtonText:'Reset PIN',cancelButtonText:'Batal',confirmButtonColor:'#f59e0b',
        preConfirm:() => { const pin = document.getElementById('newPin').value; if(!pin || pin.length !== 4) { Swal.showValidationMessage('PIN harus 4 digit angka'); } return pin; },
        customClass:{popup:'rounded-4'}
    }).then((r) => {
        if(r.isConfirmed) {
            const form = document.getElementById('resetPinForm');
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'new_pin'; input.value = r.value;
            form.appendChild(input);
            form.submit();
        }
    });
}
</script>
@endsection
