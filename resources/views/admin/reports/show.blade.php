@extends('layouts.admin')
@section('title', 'Detail Laporan #' . $report->id)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.reports.index') }}">Laporan Penjualan</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">#{{ $report->id }}</span>
@endsection

@section('content')
<style>
    /* Lightbox Gallery */
    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 16px; }
    .gallery-item {
        border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 1; cursor: pointer;
        border: 2px solid transparent; transition: all var(--transition-fast); position: relative;
    }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-slow); }
    .gallery-item:hover { border-color: var(--primary); box-shadow: var(--shadow-md); }
    .gallery-item:hover img { transform: scale(1.05); }
    .gallery-item::after { content: '\f00e'; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; opacity: 0; transition: opacity var(--transition-fast); text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
    .gallery-item:hover::after { opacity: 1; }
    
    /* Timeline */
    .timeline { position: relative; padding-left: 24px; }
    .timeline::before { content: ''; position: absolute; left: 6px; top: 4px; bottom: 4px; width: 2px; background: var(--neutral-200); border-radius: 2px; }
    .timeline-item { position: relative; margin-bottom: 24px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--primary); border: 3px solid white; box-shadow: 0 0 0 1px var(--neutral-300); }
    .timeline-dot.success { background: var(--success); }
    .timeline-dot.danger { background: var(--danger); }
    .timeline-dot.warning { background: var(--warning); }
</style>

<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                @if($report->status == 'diproses') 
                    <span class="badge badge-warning rounded-pill px-3 py-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-clock-rotate-left me-1"></i> DIPROSES</span>
                @elseif($report->status == 'disetujui') 
                    <span class="badge badge-success rounded-pill px-3 py-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-check-circle me-1"></i> DISETUJUI</span>
                @else 
                    <span class="badge badge-danger rounded-pill px-3 py-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-circle-xmark me-1"></i> DITOLAK</span>
                @endif
                <span class="text-muted" style="font-size:13px; font-weight:600;"><i class="fa-solid fa-hashtag me-1"></i>{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="page-title">Detail Laporan Penjualan</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;"><i class="fa-regular fa-calendar me-1"></i> Dibuat pada: {{ $report->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <button onclick="window.print()" class="btn btn-light px-3 py-2 d-none d-md-inline-block" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-print me-2"></i> Cetak</button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-light px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-arrow-left me-2"></i> Kembali</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    {{-- Main Content Column --}}
    <div class="col-12 col-lg-8">
        
        {{-- Rincian Produk --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-box-open text-primary me-2"></i> Item Penjualan
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table align-middle" style="margin:0;">
                        <thead style="background: var(--neutral-50);">
                            <tr>
                                <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Produk</th>
                                <th class="text-center" style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:100px;">Kuantitas</th>
                                <th class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:180px;">Harga Satuan</th>
                                <th class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:180px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->items as $item)
                            <tr>
                                <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    <span class="fw-bold text-dark">{{ $item->product_name ?? $item->product->name ?? 'Produk Tidak Ditemukan' }}</span>
                                </td>
                                <td class="text-center" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size:13px;">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-end text-muted" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-dark" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: var(--neutral-50);">
                            <tr>
                                <td colspan="3" class="text-end" style="padding:16px 24px; border-top:2px solid var(--border-light);">
                                    <span class="text-muted fw-bold" style="font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">Total Nilai Transaksi</span>
                                </td>
                                <td class="text-end" style="padding:16px 24px; border-top:2px solid var(--border-light);">
                                    <span class="fw-bold text-primary" style="font-size:20px;">Rp {{ number_format($report->total_amount, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Foto Bukti --}}
        @if($report->images->count() > 0)
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-camera text-muted me-2"></i> Lampiran Bukti Visual
                </h5>
            </div>
            <div class="card-body p-4 bg-neutral-50">
                <div class="gallery-grid">
                    @foreach($report->images as $img)
                    <div class="gallery-item bg-white shadow-sm" onclick="openLightbox('{{ asset('storage/'.$img->image_path) }}')">
                        <img src="{{ asset('storage/'.$img->image_path) }}" alt="Bukti Transaksi">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Histori Laporan --}}
        @if($report->statusHistories->count() > 0)
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-timeline text-muted me-2"></i> Linimasa Status
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="timeline">
                    @foreach($report->statusHistories as $h)
                    <div class="timeline-item">
                        <div class="timeline-dot 
                            @if($h->to_status == 'disetujui') success
                            @elseif($h->to_status == 'ditolak') danger
                            @elseif($h->to_status == 'diproses') warning
                            @else primary @endif
                        "></div>
                        <div class="timeline-content bg-neutral-50 p-3 rounded-3 border border-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge 
                                    @if($h->to_status == 'disetujui') badge-success
                                    @elseif($h->to_status == 'ditolak') badge-danger
                                    @elseif($h->to_status == 'diproses') badge-warning
                                    @else badge-primary @endif
                                ">
                                    {{ strtoupper($h->to_status) }}
                                </span>
                                <span class="text-muted" style="font-size:12px;">
                                    <i class="fa-regular fa-clock me-1"></i>{{ $h->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <div style="font-size: 13px; color: var(--text-secondary);">
                                Diperbarui oleh <strong class="text-dark">{{ $h->user->name ?? 'Sistem' }}</strong>
                            </div>
                            
                            @if($h->notes)
                                <div class="mt-2 p-2 bg-white rounded text-dark border border-light" style="font-size:13px; font-style:italic;">
                                    "{{ $h->notes }}"
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        {{-- Keterangan Tambahan --}}
        @if($report->notes)
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4 border-start border-4 border-primary rounded">
                <div class="text-primary fw-bold mb-2" style="font-size:13px; text-transform:uppercase;"><i class="fa-solid fa-note-sticky me-2"></i>Catatan Pengirim</div>
                <div class="text-dark" style="font-size:15px; line-height:1.6;">{{ $report->notes }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Side Column --}}
    <div class="col-12 col-lg-4">
        
        {{-- Informasi Transaksi Card --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0" style="font-size:16px;">Ringkasan Informasi</h5>
            </div>
            <div class="card-body p-4">
                {{-- Reporter Info --}}
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-light">
                    <div style="width:48px;height:48px;border-radius:50%;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:bold;">
                        {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-muted fw-bold mb-1" style="font-size:11px; text-transform:uppercase;">Dilaporkan Oleh</div>
                        <div class="fw-bold text-dark" style="font-size:15px;">{{ $report->user->name ?? 'Tidak Diketahui' }}</div>
                        <div class="text-muted" style="font-size:12px;"><i class="fa-solid fa-store me-1"></i>{{ $report->store->name ?? '-' }}</div>
                    </div>
                </div>
                
                {{-- Details --}}
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted" style="font-size:13px;">Tgl Transaksi</span>
                        <span class="fw-semibold text-dark" style="font-size:14px;">{{ \Carbon\Carbon::parse($report->transaction_date)->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted" style="font-size:13px;">Tgl Kirim Laporan</span>
                        <span class="fw-semibold text-dark" style="font-size:14px;">{{ $report->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted" style="font-size:13px;">Total Item Fisik</span>
                        <span class="fw-bold text-dark px-2 py-1 bg-light rounded" style="font-size:14px;">{{ $report->total_items }} Unit</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Panel (Sticky) --}}
        <div class="card shadow-sm border-0" style="border-radius:var(--radius-lg); position: sticky; top: 90px;">
            <div class="card-body p-4 text-center">
                <h5 class="fw-bold mb-4" style="font-size:16px;">Tindakan Administrator</h5>
                
                @if($report->status == 'diproses')
                    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaToko())
                    <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST" id="approve-report" class="d-none">@csrf</form>
                    
                    <button type="button" class="btn btn-success w-100 mb-3 px-4 py-3" style="font-weight:700; border-radius:12px; box-shadow:0 4px 12px rgba(16,185,129,0.25);" onclick="confirmApprove()">
                        <i class="fa-solid fa-check-circle me-2"></i> Setujui Laporan
                    </button>
                    
                    <button type="button" class="btn btn-outline-danger w-100 mb-3 px-4 py-3" style="font-weight:600; border-radius:12px;" onclick="showReject()">
                        <i class="fa-solid fa-xmark-circle me-2"></i> Tolak Laporan
                    </button>
                    @endif
                @elseif($report->status == 'ditolak')
                    <div class="bg-danger-50 p-3 text-start mb-4 border border-danger-100" style="border-radius:12px;">
                        <div class="fw-bold text-danger-700 mb-2" style="font-size:14px;"><i class="fa-solid fa-circle-exclamation me-1"></i>Alasan Penolakan:</div>
                        <div class="text-danger" style="font-size:13px; line-height:1.5;">{{ $report->rejection_reason }}</div>
                    </div>
                    @if($report->user_id == auth()->id() || auth()->user()->isAdmin())
                    <a href="{{ route('karyawan.reports.edit', $report->id) }}" class="btn btn-warning w-100 mb-3 px-4 py-3 text-white" style="font-weight:700; border-radius:12px; box-shadow:0 4px 12px rgba(245,158,11,0.25);">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Perbaiki Data Laporan
                    </a>
                    @endif
                @elseif($report->status == 'disetujui')
                    <div class="bg-success-50 p-4 mb-4 border border-success-100 text-center" style="border-radius:12px;">
                        <div style="width:64px;height:64px;border-radius:50%;background:var(--success);color:white;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 16px;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="fw-bold text-success-700 mb-1" style="font-size:18px;">Laporan Sah</div>
                        <div class="text-success" style="font-size:13px;">Omzet dan pengurangan stok telah dicatat permanen dalam sistem.</div>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>

<!-- Lightbox Modal Overlay -->
<div class="lightbox" id="lightbox" onclick="this.classList.remove('show')">
    <button class="lightbox-close"><i class="fa-solid fa-xmark"></i></button>
    <img id="lightboxImg" src="">
</div>

<style>
    /* Print Styles */
    @media print {
        .page-actions, .card:last-child { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        body { background: white; }
    }
</style>

<script>
function confirmApprove() {
    Swal.fire({
        title: 'Setujui Laporan?',
        html: `Pastikan semua bukti dan nominal (<strong>Rp {{ number_format($report->total_amount, 0, ',', '.') }}</strong>) sudah sesuai.<br>Aksi ini akan mencatat omzet secara permanen.`,
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#2b9348',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Ya, Setujui',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('approve-report').submit(); 
    });
}

function showReject() {
    Swal.fire({
        title: 'Tolak Laporan',
        html: '<div class="form-group text-start mt-3 mb-0"><label class="form-label fw-semibold mb-2">Alasan Penolakan <span class="text-danger">*</span></label><textarea id="rejectReason" class="form-control" rows="4" placeholder="Jelaskan alasan mengapa laporan ini ditolak agar kasir dapat memperbaikinya... (min. 10 karakter)" required></textarea></div>',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-xmark me-1"></i> Tolak Laporan',
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
