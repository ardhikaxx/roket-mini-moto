@extends('layouts.admin')
@section('title', 'Detail Cabang: ' . $store->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stores.index') }}">Toko / Cabang</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $store->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge {{ $store->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill px-2" style="font-weight:600; font-size:11px;">
                    <i class="fa-solid {{ $store->is_active ? 'fa-check-circle' : 'fa-circle-xmark' }} me-1"></i> {{ $store->is_active ? 'Cabang Aktif' : 'Cabang Nonaktif' }}
                </span>
                <span class="badge bg-light text-dark border px-2 font-monospace" style="font-weight:600; font-size:12px;">{{ $store->code }}</span>
            </div>
            <h1 class="page-title">{{ $store->name }}</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;"><i class="fa-solid fa-location-dot me-1"></i> {{ $store->address }}</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.stores.index') }}" class="btn btn-light px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-arrow-left me-2"></i> Kembali</a>
            <a href="{{ route('admin.stores.edit', $store->id) }}" class="btn btn-primary px-4 py-2" style="font-weight:600; box-shadow:0 4px 12px rgba(230,57,70,0.25);"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Cabang</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    {{-- Kolom Kiri: Profil Toko --}}
    <div class="col-12 col-lg-4">
        
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
            {{-- Foto Toko --}}
            @if($store->photo)
                <div style="width:100%; height:240px; background:var(--neutral-100); position:relative; overflow:hidden;">
                    <img src="{{ asset('storage/'.$store->photo) }}" alt="{{ $store->name }}" style="width:100%; height:100%; object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('assets/images/no-image.png') }}';">
                </div>
            @else
                <div style="width:100%; height:220px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #e63946 100%); display:flex; flex-direction:column; align-items:center; justify-content:center; color:white; position:relative; overflow:hidden;">
                    <i class="fa-solid fa-store position-absolute" style="font-size:12rem; right:-20px; bottom:-30px; opacity:0.08; color:white;"></i>
                    <div class="text-center p-3 position-relative" style="z-index:2;">
                        <div class="mb-2" style="width:60px; height:60px; border-radius:50%; background:rgba(255,255,255,0.15); backdrop-filter:blur(8px); display:inline-flex; align-items:center; justify-content:center; border:1px solid rgba(255,255,255,0.25);">
                            <i class="fa-solid fa-store fs-3 text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-1 text-white">{{ $store->name }}</h5>
                        <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-semibold mt-1" style="font-size:11px; opacity:0.95;">
                            <i class="fa-regular fa-image me-1 text-muted"></i> Foto Belum Diunggah
                        </span>
                    </div>
                </div>
            @endif

            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="font-size:16px;">Informasi Kontak & Operasional</h5>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3">
                        <div style="width:36px;height:36px;border-radius:8px;background:var(--neutral-100);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-bold mb-1" style="font-size:11px; text-transform:uppercase;">Alamat Lengkap</div>
                            <div class="text-dark" style="font-size:14px; line-height:1.5;">{{ $store->address }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div style="width:36px;height:36px;border-radius:8px;background:var(--neutral-100);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-bold mb-1" style="font-size:11px; text-transform:uppercase;">Nomor Telepon</div>
                            <div class="text-dark fw-semibold" style="font-size:14px;">{{ $store->phone ?? 'Belum ada nomor telepon' }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div style="width:36px;height:36px;border-radius:8px;background:var(--neutral-100);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="text-muted fw-bold mb-1" style="font-size:11px; text-transform:uppercase;">Jam Operasional</div>
                            <div class="text-dark fw-semibold" style="font-size:14px;">{{ $store->operational_hours ?? 'Belum diatur' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Kolom Kanan: Statistik & Tim --}}
    <div class="col-12 col-lg-8">
        
        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); background:linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                    <div class="card-body p-4 text-center">
                        <div class="text-muted fw-bold mb-2" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Total Omzet Cabang</div>
                        <h4 class="fw-bold text-primary mb-0" style="font-size:22px;">Rp {{ number_format($totalOmzet,0,',','.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
                    <div class="card-body p-4 text-center">
                        <div class="text-muted fw-bold mb-2" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Laporan Disetujui</div>
                        <h4 class="fw-bold text-dark mb-0" style="font-size:24px;">{{ $totalReports }} <span class="text-muted fw-normal" style="font-size:13px;">Transaksi</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
                    <div class="card-body p-4 text-center">
                        <div class="text-muted fw-bold mb-2" style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Jumlah Anggota Tim</div>
                        <h4 class="fw-bold text-dark mb-0" style="font-size:24px;">{{ $store->users->count() }} <span class="text-muted fw-normal" style="font-size:13px;">Personel</span></h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Karyawan --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-users-gear text-primary me-2"></i> Tim Internal Cabang
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table align-middle table-hover" style="margin:0;">
                        <thead style="background: var(--neutral-50);">
                            <tr>
                                <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Personel</th>
                                <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Role</th>
                                <th class="text-center" style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Kontribusi Laporan</th>
                                <th class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($store->users as $u)
                            <tr>
                                <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:bold;">
                                            {{ strtoupper(substr($u->name,0,2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size:14px;">{{ $u->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    @if($u->role == 'admin')
                                        <span class="badge bg-danger-50 text-danger-700 border border-danger-100">Administrator</span>
                                    @elseif($u->role == 'kepala_toko')
                                        <span class="badge bg-primary-50 text-primary-700 border border-primary-100">Kepala Toko</span>
                                    @else
                                        <span class="badge bg-light text-dark border">Karyawan (Kasir)</span>
                                    @endif
                                </td>
                                <td class="text-center" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    <span class="fw-semibold text-dark">{{ $u->sales_reports_count ?? 0 }}</span>
                                </td>
                                <td class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    @if($u->is_active)
                                        <span class="badge badge-success rounded-pill px-2 py-1" style="font-size:11px; font-weight:600;">Aktif</span>
                                    @else
                                        <span class="badge badge-danger rounded-pill px-2 py-1" style="font-size:11px; font-weight:600;">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted" style="font-style:italic;">Belum ada karyawan yang ditugaskan ke cabang ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Produk Terlaris --}}
        @if($topProducts->count() > 0)
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-fire text-warning me-2"></i> 5 Produk Paling Laris
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table align-middle table-hover" style="margin:0;">
                        <tbody>
                            @foreach($topProducts as $tp)
                            <tr>
                                <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100); width:60px;">
                                    <div class="fw-bold text-muted text-center" style="font-size:16px;">#{{ $loop->iteration }}</div>
                                </td>
                                <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    <span class="fw-bold text-dark">{{ $tp->product->name ?? 'Produk Tidak Ditemukan' }}</span>
                                </td>
                                <td class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-bold" style="font-size:13px;">{{ $tp->total_qty }} Unit Terjual</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
