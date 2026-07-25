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
            <table class="table datatable" style="margin:0;">
                <thead><tr><th>Tanggal</th><th>Toko</th><th>Total Item</th><th>Total Penjualan</th><th>Status</th><th class="cell-action">Aksi</th></tr></thead>
                <tbody>
                    @foreach($reports as $r)
                    <tr>
                        <td style="font-size:13px;">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $r->store->name ?? '-' }}</td>
                        <td>{{ $r->total_items }}</td>
                        <td class="fw-semibold">Rp {{ number_format($r->total_amount,0,',','.') }}</td>
                        <td>
                            @if($r->status == 'diproses') <span class="badge badge-warning">DIPROSES</span>
                            @elseif($r->status == 'disetujui') <span class="badge badge-success">DISETUJUI</span>
                            @else <span class="badge badge-danger">DITOLAK</span>
                            @endif
                        </td>
                        <td class="cell-action">
                            <div class="dropdown">
                                <button class="btn btn-ghost btn-icon-sm" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="dropdown-menu">
                                    <a href="{{ route('admin.reports.show', $r->id) }}" class="dropdown-item"><i class="fa-regular fa-eye"></i> Detail</a>
                                    @if($r->status == 'ditolak')
                                    <a href="{{ route('karyawan.reports.edit', $r->id) }}" class="dropdown-item text-warning"><i class="fa-solid fa-pen"></i> Perbaiki</a>
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
