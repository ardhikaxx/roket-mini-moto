<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analitik & Performa Bisnis - Roket Mini Moto</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; padding: 24px; font-size: 12px; }
        
        .print-container { max-width: 900px; margin: 0 auto; background: white; border-radius: 12px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
        
        .header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; }
        .header-title h1 { font-size: 20px; font-weight: 800; color: #e63946; letter-spacing: -0.5px; }
        .header-title p { font-size: 12px; color: #64748b; margin-top: 4px; }
        
        .badge-date { background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 11px; }

        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
        .kpi-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; }
        .kpi-card.highlight { background: linear-gradient(135deg, #e63946, #b91c1c); color: white; border: none; }
        .kpi-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; margin-bottom: 6px; }
        .kpi-value { font-size: 18px; font-weight: 800; }
        
        .section-title { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; font-size: 12px; }
        th { background: #0f172a; color: white; padding: 10px 14px; text-align: left; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        tr:nth-child(even) td { background: #f8fafc; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        
        .footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 32px; pt-20px; border-top: 1px solid #e2e8f0; }
        .signature-box { text-align: center; min-width: 180px; }
        .signature-space { height: 60px; }

        @media print {
            body { background: white; padding: 0; }
            .print-container { box-shadow: none; border: none; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width:900px; margin:0 auto 16px; display:flex; justify-content:space-between; align-items:center;">
    <button onclick="window.history.back()" style="background:#e2e8f0; color:#334155; border:none; padding:8px 16px; border-radius:6px; font-weight:600; cursor:pointer;">&larr; Kembali</button>
    <button onclick="window.print()" style="background:#e63946; color:white; border:none; padding:8px 20px; border-radius:6px; font-weight:700; cursor:pointer;">Cetak / Simpan PDF</button>
</div>

<div class="print-container">
    <div class="header">
        <div class="header-title">
            <h1>ROKET MINI MOTO BONDOWOSO</h1>
            <p>Laporan Analitik & Performa Bisnis Penjualan</p>
        </div>
        <div class="badge-date">
            Dicetak: {{ now()->format('d F Y, H:i') }} WIB
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-grid">
        <div class="kpi-card highlight">
            <div class="kpi-label">Total Omzet Disetujui</div>
            <div class="kpi-value">Rp {{ number_format($totalOmzet,0,',','.') }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label" style="color:#64748b;">Total Transaksi Sukses</div>
            <div class="kpi-value" style="color:#0f172a;">{{ number_format($totalTransactions,0,',','.') }} <span style="font-size:11px;font-weight:600;">Laporan</span></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label" style="color:#64748b;">Produk Terjual</div>
            <div class="kpi-value" style="color:#0f172a;">{{ number_format($totalItems,0,',','.') }} <span style="font-size:11px;font-weight:600;">Unit</span></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label" style="color:#64748b;">Rata-rata Transaksi</div>
            <div class="kpi-value" style="color:#0f172a;">Rp {{ number_format($avgTransaction,0,',','.') }}</div>
        </div>
    </div>

    {{-- Detail Kontribusi per Cabang --}}
    <div class="section-title">
        DETAIL KONTRIBUSI OMZET PER CABANG TOKO
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:40px;" class="text-center">No</th>
                <th>Nama Cabang</th>
                <th class="text-center">Volume Transaksi</th>
                <th class="text-end">Total Omzet Disetor</th>
                <th class="text-end">Kontribusi (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($storeOmzet as $index => $so)
            @php $pct = $totalOmzet > 0 ? round(($so->total / $totalOmzet) * 100, 1) : 0; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-weight:700; color:#0f172a;">{{ $so->store->name ?? 'Cabang Tidak Diketahui' }}</td>
                <td class="text-center">{{ $so->count }} Laporan</td>
                <td class="text-end" style="font-weight:700; color:#e63946;">Rp {{ number_format($so->total,0,',','.') }}</td>
                <td class="text-end" style="font-weight:700;">{{ $pct }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data kontribusi cabang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Distribusi Kategori --}}
    <div class="section-title">
        DISTRIBUSI PENJUALAN PER KATEGORI PRODUK
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:40px;" class="text-center">No</th>
                <th>Nama Kategori Produk</th>
                <th class="text-center">Total Volume Terjual</th>
                <th class="text-end">Total Nominal Omzet (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $catIdx = 1; @endphp
            @forelse($catSummary as $name => $data)
            <tr>
                <td class="text-center">{{ $catIdx++ }}</td>
                <td style="font-weight:700; color:#0f172a;">{{ $name }}</td>
                <td class="text-center">{{ number_format($data['qty'],0,',','.') }} Unit</td>
                <td class="text-end" style="font-weight:700;">Rp {{ number_format($data['amount'],0,',','.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada data distribusi kategori.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>
            <p style="font-size:11px; color:#64748b;">Dokumen ini dihasilkan secara otomatis oleh Sistem Manajemen Roket Mini Moto.</p>
            <p style="font-size:11px; color:#64748b; margin-top:2px;">Bondowoso, Jawa Timur 68212</p>
        </div>
        <div class="signature-box">
            <p style="font-size:11px; font-weight:700; color:#0f172a;">Disetujui Oleh,</p>
            <div class="signature-space"></div>
            <p style="font-size:12px; font-weight:700; color:#0f172a; text-decoration:underline;">( Management / Admin )</p>
            <p style="font-size:10px; color:#64748b;">Roket Mini Moto</p>
        </div>
    </div>
</div>

<script>
window.onload = function() {
    setTimeout(function() {
        window.print();
    }, 500);
};
</script>

</body>
</html>
