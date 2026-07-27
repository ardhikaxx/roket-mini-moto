@extends('layouts.admin')
@section('title', 'Laporan Penjualan Saya')
@section('breadcrumb')
    <a href="{{ route('karyawan.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Laporan Saya</span>
@endsection
@section('content')
@php
    $user = auth()->user();
    $reports = \App\Models\SalesReport::where('user_id', $user->id)->with('store')->latest()->get();
    $totalApproved = $reports->where('status','disetujui')->sum('total_amount');
@endphp
<div class="page-header">
    <div class="page-header-row">
        <div><h1 class="page-title">Laporan Penjualan Saya</h1><p class="page-subtitle">Total omzet disetujui: Rp {{ number_format($totalApproved,0,',','.') }}</p></div>
        <div class="page-actions"><a href="{{ route('karyawan.reports.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Laporan Baru</a></div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover align-middle" style="margin:0; min-width:700px;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Toko</th>
                        <th style="width:100px;">Total Item</th>
                        <th style="width:180px;">Total Penjualan</th>
                        <th style="width:140px;">Status</th>
                        <th class="text-end" style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $r)
                    <tr>
                        <td><span class="text-nowrap">{{ $r->created_at->format('d/m/Y H:i') }}</span></td>
                        <td>{{ $r->store->name ?? '-' }}</td>
                        <td><span class="fw-semibold">{{ $r->total_items }}</span></td>
                        <td class="fw-bold text-primary">Rp {{ number_format($r->total_amount,0,',','.') }}</td>
                        <td>
                            @if($r->status == 'diproses') <span class="badge badge-warning rounded-pill"><i class="fa-solid fa-clock-rotate-left me-1"></i> DIPROSES</span>
                            @elseif($r->status == 'disetujui') <span class="badge badge-success rounded-pill"><i class="fa-solid fa-check-circle me-1"></i> DISETUJUI</span>
                            @else <span class="badge badge-danger rounded-pill"><i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-3" style="border:1px solid var(--border-light);background:white;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical text-secondary"></i></button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="min-width: 160px;">
                                    <a href="{{ route('admin.reports.show', $r->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-eye me-2 w-15px text-center"></i> Detail</a>
                                    @if($r->status == 'ditolak')
                                    <a href="{{ route('karyawan.reports.edit', $r->id) }}" class="dropdown-item py-2 fw-semibold text-warning" style="font-size:14px;"><i class="fa-solid fa-pen me-2 w-15px text-center"></i> Perbaiki</a>
                                    @endif
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
@endsection
