@extends('layouts.admin')
@section('title', 'Detail Laporan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.reports.index') }}">Laporan</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">#{{ $report->id }}</span>
@endsection

@section('content')
<style>
    /* Premium Header */
    .report-header { background: var(--primary-900); border-radius: var(--radius-xl); padding: 32px; color: white; position: relative; overflow: hidden; margin-bottom: 24px; box-shadow: var(--shadow-lg); }
    .report-header::after { content: ''; position: absolute; right: 0; top: 0; bottom: 0; width: 50%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05)); pointer-events: none; }
    
    /* Lightbox Gallery */
    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 16px; }
    .gallery-item {
        border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 1; cursor: pointer;
        border: 2px solid transparent; transition: all var(--transition-fast); position: relative;
    }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-slow); }
    .gallery-item:hover { border-color: var(--primary); box-shadow: var(--shadow-md); }
    .gallery-item:hover img { transform: scale(1.05); }
    .gallery-item::after { content: '\f00e'; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; opacity: 0; transition: opacity var(--transition-fast); text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
    .gallery-item:hover::after { opacity: 1; }
    
    /* Info Card */
    .info-card { background: var(--surface); border-radius: var(--radius-xl); border: 1px solid var(--border-light); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-card); }
    .info-label { font-size: 13px; color: var(--text-secondary); margin-bottom: 4px; font-weight: 500; }
    .info-value { font-size: 15px; font-weight: 600; color: var(--text); }
</style>

<div class="row">
    <!-- Main Content Column -->
    <div class="col-lg-8">
        
        <!-- Premium Header -->
        <div class="report-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-2 text-white" style="font-family: var(--font-display);">Laporan Penjualan #{{ $report->id }}</h2>
                <div class="d-flex align-items-center gap-3 text-primary-200" style="font-size: 14px;">
                    <span><i class="fa-regular fa-calendar me-2"></i>{{ $report->created_at->format('d F Y, H:i') }}</span>
                    <span><i class="fa-solid fa-store me-2"></i>{{ $report->store->name ?? '-' }}</span>
                </div>
            </div>
            <div>
                @if($report->status == 'diproses') 
                    <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.5); padding: 8px 16px; font-size: 14px;"><i class="fa-solid fa-clock me-2"></i>DIPROSES</span>
                @elseif($report->status == 'disetujui') 
                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.5); padding: 8px 16px; font-size: 14px;"><i class="fa-solid fa-check me-2"></i>DISETUJUI</span>
                @else 
                    <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.5); padding: 8px 16px; font-size: 14px;"><i class="fa-solid fa-xmark me-2"></i>DITOLAK</span>
                @endif
            </div>
        </div>

        @if($report->notes)
        <div class="info-card" style="border-left: 4px solid var(--primary);">
            <div class="info-label text-primary"><i class="fa-solid fa-note-sticky me-2"></i>Keterangan / Catatan</div>
            <div class="info-value mt-2">{{ $report->notes }}</div>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-header border-bottom-0 pb-0">
                <h5 class="fw-bold mb-0">Rincian Produk</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive rounded border border-light">
                    <table class="table mb-0">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th>Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->product_name ?? $item->product->name ?? '-' }}</td>
                                <td class="text-center"><span class="badge badge-neutral">{{ $item->quantity }}</span></td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-neutral-50">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Penjualan</td>
                                <td class="text-end fw-bold fs-5 text-primary">Rp {{ number_format($report->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($report->images->count() > 0)
        <div class="card mb-4">
            <div class="card-header border-bottom-0 pb-0">
                <h5 class="fw-bold mb-0">Foto Bukti ({{ $report->images->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="gallery-grid">
                    @foreach($report->images as $img)
                    <div class="gallery-item" onclick="openLightbox('{{ asset('storage/'.$img->image_path) }}')">
                        <img src="{{ asset('storage/'.$img->image_path) }}" alt="Bukti Transaksi">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($report->statusHistories->count() > 0)
        <div class="card mb-4">
            <div class="card-header border-bottom-0 pb-0">
                <h5 class="fw-bold mb-0">Histori Laporan</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($report->statusHistories as $h)
                    <div class="timeline-item">
                        <div class="timeline-dot 
                            @if($h->to_status == 'disetujui') success
                            @elseif($h->to_status == 'ditolak') danger
                            @elseif($h->to_status == 'diproses') warning
                            @else primary @endif
                        "></div>
                        <div class="timeline-content">
                            <div class="fw-semibold" style="font-size:14px;">
                                Status: <span class="text-uppercase">{{ $h->to_status }}</span>
                            </div>
                            <div style="font-size: 13px; color: var(--text-secondary);" class="mt-1">
                                Oleh <strong>{{ $h->user->name ?? 'Sistem' }}</strong> &bull; {{ $h->created_at->diffForHumans() }}
                                @if($h->notes)
                                    <div class="mt-2 p-2 bg-neutral-50 rounded text-dark">Catatan: {{ $h->notes }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Side Column -->
    <div class="col-lg-4">
        
        <div class="info-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="avatar bg-primary-100 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px; font-weight: bold;">
                    {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="info-label mb-0">Dilaporkan Oleh</div>
                    <div class="info-value">{{ $report->user->name ?? '-' }}</div>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-6">
                    <div class="info-label">Tanggal Transaksi</div>
                    <div class="info-value">{{ $report->transaction_date }}</div>
                </div>
                <div class="col-6">
                    <div class="info-label">Total Item</div>
                    <div class="info-value">{{ $report->total_items }}</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-lg" style="position: sticky; top: 90px;">
            <div class="card-body p-4 text-center">
                <h5 class="fw-bold mb-4">Tindakan</h5>
                
                @if($report->status == 'diproses')
                    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaToko())
                    <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST" id="approve-report" class="d-none">@csrf</form>
                    
                    <button type="button" class="btn btn-success btn-lg w-100 mb-3 rounded-pill fw-bold" onclick="confirmApprove()">
                        <i class="fa-solid fa-check-circle fs-5"></i> Setujui Laporan
                    </button>
                    
                    <button type="button" class="btn btn-danger btn-lg w-100 mb-3 rounded-pill fw-bold" onclick="showReject()">
                        <i class="fa-solid fa-times-circle fs-5"></i> Tolak Laporan
                    </button>
                    @endif
                @elseif($report->status == 'ditolak')
                    <div class="bg-danger-50 text-danger-700 p-3 rounded-4 text-start mb-4 border border-danger-100">
                        <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-2"></i>Alasan Ditolak:</div>
                        <div style="font-size: 13px;">{{ $report->rejection_reason }}</div>
                    </div>
                    @if($report->user_id == auth()->id() || auth()->user()->isAdmin())
                    <a href="{{ route('karyawan.reports.edit', $report->id) }}" class="btn btn-warning btn-lg w-100 mb-3 rounded-pill fw-bold text-white">
                        <i class="fa-solid fa-pen"></i> Perbaiki Laporan
                    </a>
                    @endif
                @elseif($report->status == 'disetujui')
                    <div class="bg-success-50 text-success-700 p-4 rounded-4 mb-4 border border-success-100">
                        <i class="fa-solid fa-check-circle fs-1 mb-2"></i>
                        <div class="fw-bold fs-5">Laporan Disetujui</div>
                        <div style="font-size: 14px;" class="mt-1">Omzet telah tercatat di sistem</div>
                    </div>
                @endif
                
                <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost w-100 rounded-pill mt-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal Overlay -->
<div class="lightbox" id="lightbox" onclick="this.classList.remove('show')">
    <button class="lightbox-close"><i class="fa-solid fa-xmark"></i></button>
    <img id="lightboxImg" src="">
</div>

<script>
function confirmApprove() {
    Swal.fire({
        title: 'Setujui Laporan?',
        text: 'Pastikan bukti dan nominal sudah sesuai. Omzet akan langsung tercatat.',
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: '<i class="fa-solid fa-check"></i> Ya, Setujui',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('approve-report').submit(); 
    });
}

function showReject() {
    Swal.fire({
        title: 'Tolak Laporan',
        html: '<div class="form-group text-start mt-3"><label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label><textarea id="rejectReason" class="form-control" rows="4" placeholder="Tuliskan alasan mengapa laporan ini ditolak... (min. 10 karakter)" required></textarea></div>',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: '<i class="fa-solid fa-times"></i> Tolak Laporan',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' },
        preConfirm: () => { 
            const r = document.getElementById('rejectReason').value; 
            if(!r || r.length < 10) { 
                Swal.showValidationMessage('Alasan penolakan wajib diisi (minimal 10 karakter)'); 
                return false;
            } 
            return r; 
        }
    }).then((res) => {
        if(res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST'; 
            form.action = '{{ route("admin.reports.reject", $report->id) }}';
            form.innerHTML = '@csrf<input type="hidden" name="rejection_reason" value="' + res.value.replace(/"/g, '&quot;') + '">';
            document.body.appendChild(form); 
            form.submit();
        }
    });
}

function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('show');
}
</script>
@endsection
