@extends('layouts.admin')
@section('title', 'Profil Pengguna: ' . $user->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.users.index') }}">Pengguna</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $user->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                @if($user->isAdmin()) 
                    <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-size:11px; font-weight:700; letter-spacing:0.5px;"><i class="fa-solid fa-user-shield me-1"></i> ADMINISTRATOR</span>
                @elseif($user->isKepalaToko()) 
                    <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background-color: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; font-size:11px; font-weight:700; letter-spacing:0.5px;"><i class="fa-solid fa-user-tie me-1"></i> KEPALA TOKO</span>
                @else 
                    <span class="badge px-3 py-2 rounded-pill shadow-sm" style="background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; font-size:11px; font-weight:700; letter-spacing:0.5px;"><i class="fa-solid fa-cash-register me-1"></i> KARYAWAN</span>
                @endif
                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill px-2 py-1" style="font-weight:600; font-size:11px;">
                    <i class="fa-solid {{ $user->is_active ? 'fa-check-circle' : 'fa-circle-xmark' }} me-1"></i> {{ $user->is_active ? 'Akun Aktif' : 'Akses Diblokir' }}
                </span>
            </div>
            <h1 class="page-title">{{ $user->name }}</h1>
            <p class="page-subtitle text-muted font-monospace" style="font-size:13px;"><i class="fa-solid fa-at me-1"></i>{{ $user->username }}</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-arrow-left me-2"></i> Kembali</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    {{-- Kolom Kiri: Profil Identitas --}}
    <div class="col-12 col-lg-4">
        
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-body p-4 text-center">
                <div class="position-relative d-inline-block mb-3">
                    <div style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg, var(--primary), var(--primary-600));color:white;display:flex;align-items:center;justify-content:center;font-size:42px;font-weight:bold;margin:0 auto;box-shadow:0 8px 16px rgba(230,57,70,0.2);">
                        @if($user->photo)
                            <img src="{{ asset('storage/'.$user->photo) }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        @else
                            {{ strtoupper(substr($user->name,0,2)) }}
                        @endif
                    </div>
                    @if($user->is_active)
                        <div class="position-absolute" style="bottom:5px;right:5px;width:20px;height:20px;background:var(--success);border-radius:50%;border:3px solid white;"></div>
                    @else
                        <div class="position-absolute" style="bottom:5px;right:5px;width:20px;height:20px;background:var(--danger);border-radius:50%;border:3px solid white;"></div>
                    @endif
                </div>
                
                <h4 class="fw-bold mb-1" style="font-size:18px;">{{ $user->name }}</h4>
                <div class="text-muted mb-4" style="font-size:13px;">Diperbarui: {{ $user->updated_at->diffForHumans() }}</div>
                
                <div class="d-flex flex-column gap-3 text-start bg-neutral-50 p-3 rounded-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-phone text-secondary" style="width:20px;text-align:center;"></i>
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;font-weight:700;">Telepon</div>
                            <div class="fw-semibold text-dark" style="font-size:14px;">{{ $user->phone ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-clock-rotate-left text-secondary" style="width:20px;text-align:center;"></i>
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;font-weight:700;">Aktivitas Terakhir</div>
                            <div class="fw-semibold text-dark" style="font-size:14px;">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah login' }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius:8px;">
                        <i class="fa-regular fa-pen-to-square me-2"></i> Edit Data Pengguna
                    </a>
                    <button class="btn btn-outline-warning w-100 py-2 fw-bold" style="border-radius:8px;" onclick="showResetPin()">
                        <i class="fa-solid fa-key me-2"></i> Reset PIN Keamanan
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Kolom Kanan: Kinerja & Aktivitas --}}
    <div class="col-12 col-lg-8">
        
        {{-- KPI Kinerja Laporan --}}
        <h5 class="fw-bold mb-3" style="font-size:15px;"><i class="fa-solid fa-chart-pie text-muted me-2"></i>Statistik Kinerja Laporan</h5>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); background:linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                    <div class="card-body p-4 text-center">
                        <div class="text-muted fw-bold mb-2" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Laporan Disetujui</div>
                        <h4 class="fw-bold text-success mb-0" style="font-size:24px;">{{ $approvedCount }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
                    <div class="card-body p-4 text-center">
                        <div class="text-muted fw-bold mb-2" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Sedang Diproses</div>
                        <h4 class="fw-bold text-warning mb-0" style="font-size:24px;">{{ $pendingCount }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
                    <div class="card-body p-4 text-center">
                        <div class="text-muted fw-bold mb-2" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Ditolak (Perlu Perbaikan)</div>
                        <h4 class="fw-bold text-danger mb-0" style="font-size:24px;">{{ $rejectedCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Bisnis & Penugasan --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-briefcase text-primary me-2"></i> Ringkasan Kontribusi & Penugasan
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6 border-end border-light">
                        <div class="text-muted fw-bold mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Total Omzet Disetorkan</div>
                        <div class="fw-bold text-primary mb-3" style="font-size:22px;">Rp {{ number_format($totalApprovedAmount,0,',','.') }}</div>
                        
                        <div class="text-muted fw-bold mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Total Laporan Dibuat</div>
                        <div class="fw-bold text-dark" style="font-size:18px;">{{ $user->salesReports->count() }} <span class="text-muted fw-normal" style="font-size:13px;">Berkas</span></div>
                    </div>
                    <div class="col-md-6">
                        @if(!$user->isAdmin())
                            <div class="text-muted fw-bold mb-2" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Lokasi Penugasan (Toko)</div>
                            @if($user->stores->isEmpty())
                                <div class="bg-neutral-50 p-3 rounded text-center text-muted" style="font-style:italic; font-size:13px; border:1px dashed var(--border-light);">
                                    Belum ada toko yang ditugaskan kepada pengguna ini.
                                </div>
                            @else
                                <div class="d-flex flex-column gap-2 mt-2">
                                    @foreach($user->stores as $s) 
                                        <div class="d-flex align-items-center gap-2 bg-light p-2 rounded border border-light">
                                            <i class="fa-solid fa-store text-primary opacity-75"></i>
                                            <span class="fw-semibold text-dark" style="font-size:13px;">{{ $s->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center bg-primary-50 rounded p-3 border border-primary-100">
                                <i class="fa-solid fa-earth-asia text-primary mb-2" style="font-size:24px;"></i>
                                <div class="fw-bold text-primary-700" style="font-size:14px;">Akses Administrator</div>
                                <div class="text-primary-700 opacity-75" style="font-size:12px;">Pengguna ini memiliki hak penuh atas seluruh data sistem dan seluruh cabang toko.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Laporan Terbaru (Histori) --}}
        @if($user->salesReports->count() > 0)
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-clock-rotate-left text-muted me-2"></i> 10 Histori Laporan Terakhir
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table align-middle table-hover" style="margin:0;">
                        <thead style="background: var(--neutral-50);">
                            <tr>
                                <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; border-bottom:1px solid var(--border-light);">Tgl Dibuat</th>
                                <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; border-bottom:1px solid var(--border-light);">Cabang</th>
                                <th class="text-end" style="padding:12px 20px; font-size:12px; text-transform:uppercase; border-bottom:1px solid var(--border-light);">Total Nilai</th>
                                <th class="text-end" style="padding:12px 20px; font-size:12px; text-transform:uppercase; border-bottom:1px solid var(--border-light);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->salesReports()->latest()->take(10)->get() as $r)
                            <tr>
                                <td style="padding:12px 20px; border-bottom:1px solid var(--neutral-100);">
                                    <div class="fw-semibold text-dark" style="font-size:13px;">{{ $r->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $r->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td style="padding:12px 20px; border-bottom:1px solid var(--neutral-100);">
                                    <span class="text-dark" style="font-size:13px;"><i class="fa-solid fa-store text-muted me-1"></i>{{ $r->store->name ?? '-' }}</span>
                                </td>
                                <td class="text-end fw-bold text-dark" style="padding:12px 20px; border-bottom:1px solid var(--neutral-100); font-size:14px;">
                                    Rp {{ number_format($r->total_amount,0,',','.') }}
                                </td>
                                <td class="text-end" style="padding:12px 20px; border-bottom:1px solid var(--neutral-100);">
                                    @if($r->status == 'diproses') 
                                        <span class="badge badge-warning rounded-pill px-2 py-1" style="font-size:10px;"><i class="fa-solid fa-clock me-1"></i>DIPROSES</span>
                                    @elseif($r->status == 'disetujui') 
                                        <span class="badge badge-success rounded-pill px-2 py-1" style="font-size:10px;"><i class="fa-solid fa-check me-1"></i>DISETUJUI</span>
                                    @else 
                                        <span class="badge badge-danger rounded-pill px-2 py-1" style="font-size:10px;"><i class="fa-solid fa-xmark me-1"></i>DITOLAK</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 text-center bg-light border-top border-light">
                    <a href="{{ route('admin.reports.index') }}?user_id={{ $user->id }}" class="btn btn-sm btn-outline-secondary" style="font-weight:600; font-size:13px;">Lihat Seluruh Laporan</a>
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
        title: 'Reset PIN Karyawan',
        html: `
            <div class="form-group text-start mt-3 mb-0">
                <label class="form-label fw-semibold mb-2">Masukkan PIN Baru (4 Digit)</label>
                <input type="password" id="newPin" class="form-control form-control-lg text-center" maxlength="4" pattern="[0-9]{4}" placeholder="••••" style="letter-spacing:10px; font-size:24px;">
                <small class="text-muted mt-2 d-block">PIN harus terdiri dari tepat 4 digit angka.</small>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-key me-1"></i> Konfirmasi Reset',
        cancelButtonText: 'Batal',
        preConfirm: () => { 
            const pin = document.getElementById('newPin').value; 
            if(!pin || pin.length !== 4 || !/^\d+$/.test(pin)) { 
                Swal.showValidationMessage('PIN tidak valid. Harus 4 digit angka.'); 
                return false;
            } 
            return pin; 
        },
        customClass: { popup: 'rounded-4' }
    }).then((r) => {
        if(r.isConfirmed) {
            const form = document.getElementById('resetPinForm');
            const input = document.createElement('input');
            input.type = 'hidden'; 
            input.name = 'new_pin'; 
            input.value = r.value;
            form.appendChild(input);
            form.submit();
        }
    });
}
</script>
@endsection
