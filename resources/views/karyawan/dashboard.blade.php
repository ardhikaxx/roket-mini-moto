
@extends("layouts.admin")
@section("title", "Dashboard Karyawan")
@section("content")
    <h2 class="fw-bold mb-4">Selamat Datang, {{ auth()->user()->name }}</h2>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white p-3 shadow-sm border-0">
                <h5>Laporan Disetujui</h5>
                <h3>{{ \App\Models\SalesReport::where("user_id", auth()->id())->where("status","disetujui")->count() }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark p-3 shadow-sm border-0">
                <h5>Laporan Diproses</h5>
                <h3>{{ \App\Models\SalesReport::where("user_id", auth()->id())->where("status","diproses")->count() }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white p-3 shadow-sm border-0">
                <h5>Laporan Ditolak</h5>
                <h3>{{ \App\Models\SalesReport::where("user_id", auth()->id())->where("status","ditolak")->count() }}</h3>
            </div>
        </div>
    </div>
@endsection
