
@extends("layouts.admin")
@section("title", "Dashboard Admin")
@section("content")
    <h2 class="fw-bold mb-4">Dashboard Administrator</h2>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3 shadow-sm border-0">
                <h5>Total Omzet</h5>
                <h3>Rp {{ number_format(\App\Models\SalesReport::where("status","disetujui")->sum("total_amount"), 0, ",",".") }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3 shadow-sm border-0">
                <h5>Total Penjualan</h5>
                <h3>{{ \App\Models\SalesReport::where("status","disetujui")->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3 shadow-sm border-0">
                <h5>Menunggu Persetujuan</h5>
                <h3>{{ \App\Models\SalesReport::where("status","diproses")->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white p-3 shadow-sm border-0">
                <h5>Total Toko</h5>
                <h3>{{ \App\Models\Store::count() }}</h3>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <h5 class="fw-bold">Grafik Penjualan</h5>
        <p class="text-muted">Modul grafik dapat diimplementasikan di sini (misal dengan Chart.js).</p>
    </div>
@endsection
