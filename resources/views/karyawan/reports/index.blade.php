
@extends("layouts.admin")
@section("title", "Laporan Penjualan Saya")
@section("content")
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Laporan Penjualan Saya</h2>
        <button class="btn btn-primary" onclick="Swal.fire('Buat Laporan', 'Segera Hadir!', 'info')">+ Buat Laporan</button>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table datatable table-bordered">
                <thead><tr><th>Tanggal</th><th>Toko</th><th>Total Item</th><th>Total Penjualan</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach(\App\Models\SalesReport::where("user_id", auth()->id())->with("store")->orderByDesc("created_at")->get() as $report)
                    <tr>
                        <td>{{ $report->transaction_date }}</td>
                        <td>{{ $report->store?->name }}</td>
                        <td>{{ $report->total_items }}</td>
                        <td>Rp {{ number_format($report->total_amount,0,",",".") }}</td>
                        <td>
                            @if($report->status == "diproses") <span class="badge bg-warning text-dark">DIPROSES</span>
                            @elseif($report->status == "disetujui") <span class="badge bg-success">DISETUJUI</span>
                            @else <span class="badge bg-danger">DITOLAK</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary">Detail</button>
                            @if($report->status == "ditolak")
                            <button class="btn btn-sm btn-warning text-dark">Perbaiki</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
