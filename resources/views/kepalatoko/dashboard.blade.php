@extends('layouts.admin')
@section('title', 'Dashboard Kepala Toko')
@section('content')
    <h2 class="fw-bold mb-4">Dashboard Kepala Toko</h2>
    <p class="text-muted">Menampilkan performa toko yang berada di bawah tanggung jawab Anda.</p>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3 shadow-sm border-0">
                <h5>Omzet Cabang</h5>
                <h3>Rp {{ number_format(\App\Models\SalesReport::whereIn('store_id', auth()->user()->stores->pluck('id'))->where('status','disetujui')->sum('total_amount'), 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3 shadow-sm border-0">
                <h5>Total Penjualan</h5>
                <h3>{{ \App\Models\SalesReport::whereIn('store_id', auth()->user()->stores->pluck('id'))->where('status','disetujui')->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3 shadow-sm border-0">
                <h5>Menunggu Persetujuan</h5>
                <h3>{{ \App\Models\SalesReport::whereIn('store_id', auth()->user()->stores->pluck('id'))->where('status','diproses')->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white p-3 shadow-sm border-0">
                <h5>Total Karyawan</h5>
                <h3>{{ \App\Models\User::whereHas('stores', function($q) { $q->whereIn('stores.id', auth()->user()->stores->pluck('id')); })->where('role', 'karyawan')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <h5 class="fw-bold">Laporan Penjualan Cabang</h5>
        <div class="table-responsive mt-3">
            <table class="table datatable table-bordered">
                <thead><tr><th>Tanggal</th><th>Toko</th><th>Kasir</th><th>Total Penjualan</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach(\App\Models\SalesReport::whereIn('store_id', auth()->user()->stores->pluck('id'))->with(['store', 'user'])->orderByDesc('created_at')->limit(10)->get() as $report)
                    <tr>
                        <td>{{ $report->transaction_date }}</td>
                        <td>{{ $report->store?->name }}</td>
                        <td>{{ $report->user?->name }}</td>
                        <td>Rp {{ number_format($report->total_amount,0,',','.') }}</td>
                        <td>
                            @if($report->status == 'diproses') <span class="badge bg-warning text-dark">DIPROSES</span>
                            @elseif($report->status == 'disetujui') <span class="badge bg-success">DISETUJUI</span>
                            @else <span class="badge bg-danger">DITOLAK</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
