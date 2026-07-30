@extends('layouts.admin')
@section('title', 'Target Penjualan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Target Penjualan</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-bullseye text-primary me-2"></i>Target Penjualan</h1>
            <p class="page-subtitle">Tetapkan dan pantau target omzet bulanan per toko & karyawan</p>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
    <div class="card-body p-4 bg-neutral-50">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Bulan</label>
                <select name="month" class="form-select" onchange="this.form.submit()">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                        <option value="{{ $i+1 }}" {{ ($month ?? now()->month) == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Tahun</label>
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <a href="{{ route('admin.sales-targets.index') }}" class="btn btn-light border fw-bold"><i class="fa-solid fa-rotate-right me-1"></i> Bulan Ini</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Buat Target Baru</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.sales-targets.store') }}" class="row g-3">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Toko (opsional)</label>
                <select name="store_id" class="form-select">
                    <option value="">-- Pilih Toko --</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Karyawan (opsional)</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}">{{ $e->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-bold">Target Omzet (Rp)</label>
                <input type="text" name="target_amount" inputmode="numeric" class="form-control input-rupiah" placeholder="10.000.000" required>
            </div>
            <div class="col-12 col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary fw-bold w-100"><i class="fa-solid fa-check"></i></button>
            </div>
            <div class="col-12">
                <small class="text-muted">Kosongkan toko & karyawan untuk target global. Isi salah satu atau keduanya.</small>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($targets as $target)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="fw-bold text-dark" style="font-size:15px;">
                            @if($target->store)
                                <i class="fa-solid fa-store text-primary me-1"></i> {{ $target->store->name }}
                            @elseif($target->user)
                                <i class="fa-solid fa-user text-primary me-1"></i> {{ $target->user->name }}
                            @else
                                <i class="fa-solid fa-globe text-primary me-1"></i> Global
                            @endif
                        </div>
                        <div class="text-muted" style="font-size:12px;">
                            {{ $target->monthName }} {{ $target->year }}
                            @if($target->store && $target->user)
                                &middot; {{ $target->user->name }}
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.sales-targets.destroy', $target) }}" onsubmit="return confirm('Hapus target ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light border text-danger"><i class="fa-solid fa-trash-can"></i></button>
                    </form>
                </div>

                @php
                    $achieved = $target->achieved;
                    $percent = $target->percentage;
                    $barColor = $percent >= 100 ? 'success' : ($percent >= 75 ? 'warning' : ($percent >= 50 ? 'info' : 'danger'));
                @endphp

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold" style="font-size:13px;">Target: Rp {{ number_format($target->target_amount,0,',','.') }}</span>
                    <span class="fw-bold" style="font-size:13px;color:var(--{{ $barColor }});">{{ $percent }}%</span>
                </div>
                <div class="progress mb-2" style="height:10px;border-radius:10px;background:var(--neutral-100);">
                    <div class="progress-bar bg-{{ $barColor }}" role="progressbar" style="width:{{ $percent }}%;border-radius:10px;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between text-muted" style="font-size:12px;">
                    <span>Tercapai: Rp {{ number_format($achieved,0,',','.') }}</span>
                    <span>{{ $percent >= 100 ? 'Selesai' : 'Sisa: Rp '. number_format(max(0, $target->target_amount - $achieved),0,',','.') }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius:var(--radius-lg);">
            <div class="card-body text-center py-5">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--neutral-100);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 16px;"><i class="fa-solid fa-bullseye"></i></div>
                <p class="text-muted fw-semibold m-0">Belum ada target untuk bulan ini.</p>
                <p class="text-muted" style="font-size:13px;">Gunakan form di atas untuk menetapkan target penjualan.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
