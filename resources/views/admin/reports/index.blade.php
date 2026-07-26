@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Laporan Penjualan</span>
@endsection
@section('content')
@php
    $allCount = \App\Models\SalesReport::count();
    $pendingCount = \App\Models\SalesReport::where('status','diproses')->count();
    $approvedCount = \App\Models\SalesReport::where('status','disetujui')->count();
    $rejectedCount = \App\Models\SalesReport::where('status','ditolak')->count();
    $user = auth()->user();
    if ($user->isAdmin()) {
        $reports = \App\Models\SalesReport::with(['store','user','images'])->latest()->get();
    } elseif ($user->isKepalaToko()) {
        $reports = \App\Models\SalesReport::whereIn('store_id', $user->stores->pluck('id'))->with(['store','user','images'])->latest()->get();
    } else {
        $reports = \App\Models\SalesReport::where('user_id', $user->id)->with(['store','images'])->latest()->get();
    }
@endphp
<div class="page-header">
    <div class="page-header-row">
        <div><h1 class="page-title">Laporan Penjualan</h1><p class="page-subtitle">{{ $allCount }} total laporan</p></div>
        @if($user->isKaryawan())
        <div class="page-actions"><a href="{{ route('karyawan.reports.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Laporan</a></div>
        @endif
    </div>
</div>

<div class="row g-4 mb-4 stagger-1">
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:pointer;" onclick="filterTable('all')"><div class="stat-label text-secondary fw-semibold mb-1">Semua</div><div class="stat-value" style="font-size:28px;">{{ $allCount }}</div></div></div>
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:pointer;border-color:var(--warning-100);" onclick="filterTable('diproses')"><div class="stat-label text-secondary fw-semibold mb-1">Menunggu</div><div class="stat-value" style="font-size:28px;color:var(--warning);">{{ $pendingCount }}</div></div></div>
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:pointer;border-color:var(--success-100);" onclick="filterTable('disetujui')"><div class="stat-label text-secondary fw-semibold mb-1">Disetujui</div><div class="stat-value" style="font-size:28px;color:var(--success);">{{ $approvedCount }}</div></div></div>
    <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card" style="padding:20px;cursor:pointer;border-color:var(--danger-100);" onclick="filterTable('ditolak')"><div class="stat-label text-secondary fw-semibold mb-1">Ditolak</div><div class="stat-value" style="font-size:28px;color:var(--danger);">{{ $rejectedCount }}</div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" id="reportsTable" style="margin:0;">
                <thead>
                    <tr>
                        <th style="width:50px;">Foto</th>
                        <th>Kasir</th>
                        <th>Toko</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="cell-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $r)
                    <tr data-status="{{ $r->status }}">
                        <td>
                            @if($r->images->first())
                            <div style="width:40px;height:40px;border-radius:var(--radius-sm);overflow:hidden;cursor:pointer;" onclick="openLightbox('{{ asset('storage/'.$r->images->first()->image_path) }}')">
                                <img src="{{ asset('storage/'.$r->images->first()->image_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            @else
                            <div style="width:40px;height:40px;border-radius:var(--radius-sm);background:var(--neutral-100);display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:16px;"><i class="fa-solid fa-image"></i></div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:14px;">{{ $r->user->name ?? '-' }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">{{ $r->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $r->store->name ?? '-' }}</td>
                        <td style="font-size:13px;">{{ $r->transaction_date }}</td>
                        <td>
                            @php $itemNames = $r->items->pluck('product_name')->take(3); @endphp
                            @foreach($itemNames as $in)
                                <span class="badge badge-neutral me-1">{{ $in }}</span>
                            @endforeach
                            @if($r->items->count() > 3)
                                <span class="badge badge-primary">+{{ $r->items->count()-3 }} lagi</span>
                            @endif
                        </td>
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
                                    @if($r->status == 'diproses' && ($user->isAdmin() || $user->isKepalaToko()))
                                    <form action="{{ route('admin.reports.approve', $r->id) }}" method="POST" id="approve-{{ $r->id }}" style="display:inline;">@csrf
                                        <button type="button" class="dropdown-item text-success" onclick="confirmApprove({{ $r->id }})"><i class="fa-solid fa-check"></i> Setujui</button>
                                    </form>
                                    <button type="button" class="dropdown-item text-danger" onclick="showReject({{ $r->id }})"><i class="fa-solid fa-times"></i> Tolak</button>
                                    @endif
                                    @if($user->isAdmin())
                                    <form action="{{ route('admin.reports.destroy', $r->id) }}" method="POST" id="del-report-{{ $r->id }}">@csrf @method('DELETE')
                                        <button type="button" class="dropdown-item text-danger" onclick="confirmDeleteReport({{ $r->id }})"><i class="fa-regular fa-trash-can"></i> Hapus</button>
                                    </form>
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

<script>
function filterTable(status) {
    const table = $('#reportsTable').DataTable();
    if (status === 'all') { table.search('').columns().search('').draw(); }
    else { table.column(6).search(status).draw(); }
}
function confirmApprove(id) {
    Swal.fire({title:'Setujui Laporan?',text:'Laporan yang disetujui akan masuk ke perhitungan omzet. Stok produk akan berkurang.',icon:'question',showCancelButton:true,confirmButtonColor:'#16a34a',confirmButtonText:'Ya, Setujui',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('approve-'+id).submit(); });
}
function showReject(id) {
    Swal.fire({
        title:'Tolak Laporan',
        html:'<div class="form-group"><label class="form-label text-start d-block">Alasan Penolakan <span class="required">*</span></label><textarea id="rejectReason" class="form-control" rows="3" required></textarea></div>',
        showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Tolak Laporan',cancelButtonText:'Batal',
        preConfirm:() => { const r = document.getElementById('rejectReason').value; if(!r) { Swal.showValidationMessage('Alasan penolakan wajib diisi'); } return r; },
        customClass:{popup:'rounded-4'}
    }).then((res) => {
        if(res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = '{{ url("admin/reports") }}/'+id+'/reject';
            form.innerHTML = '@csrf<input type="hidden" name="rejection_reason" value="'+res.value+'">';
            document.body.appendChild(form); form.submit();
        }
    });
}
function confirmDeleteReport(id) {
    Swal.fire({title:'Hapus Laporan?',text:'Data laporan akan dihapus permanen. Yakin?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal',customClass:{popup:'rounded-4'}})
    .then((r) => { if(r.isConfirmed) document.getElementById('del-report-'+id).submit(); });
}
</script>
@endsection
