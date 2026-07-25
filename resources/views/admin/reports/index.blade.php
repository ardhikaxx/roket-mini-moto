
@extends("layouts.admin")
@section("title", "Manajemen Laporan Penjualan")
@section("content")
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Laporan Penjualan</h2>
    </div>
    <div class="card border-0 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table datatable table-bordered">
                <thead><tr><th>Tanggal</th><th>Toko</th><th>Kasir</th><th>Total Item</th><th>Total Penjualan</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach(\App\Models\SalesReport::with(["store", "user"])->orderByDesc("created_at")->get() as $report)
                    <tr>
                        <td>{{ $report->transaction_date }}</td>
                        <td>{{ $report->store?->name }}</td>
                        <td>{{ $report->user?->name }}</td>
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
                            @if($report->status == "diproses" && auth()->user()->isAdmin())
                            <button class="btn btn-sm btn-success" onclick="Swal.fire('Konfirmasi', 'Setujui Laporan ini?', 'question')">Setujui</button>
                            <button class="btn btn-sm btn-danger" onclick="Swal.fire('Konfirmasi', 'Tolak Laporan ini?', 'warning')">Tolak</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
