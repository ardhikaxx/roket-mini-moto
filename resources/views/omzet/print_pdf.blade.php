<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analitik & Performa Bisnis - Roket Mini Moto</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #0f172a; font-size: 11px; margin: 0; padding: 0; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 2px solid #e63946; padding-bottom: 10px; }
        .brand-title { font-size: 18px; font-weight: bold; color: #e63946; text-transform: uppercase; margin: 0; }
        .brand-sub { font-size: 11px; color: #64748b; margin-top: 3px; }
        .date-badge { text-align: right; font-size: 10px; color: #475569; }

        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 10px; margin-bottom: 20px; }
        .kpi-cell { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; vertical-align: top; }
        .kpi-cell.highlight { background: #e63946; color: #ffffff; border: none; }
        .kpi-label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; margin-bottom: 4px; }
        .kpi-value { font-size: 16px; font-weight: bold; }

        .section-header { font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 8px; border-left: 3px solid #e63946; padding-left: 8px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .data-table th { background-color: #0f172a; color: #ffffff; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; font-weight: bold; }
        .data-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .data-table tr:nth-child(even) td { background-color: #f8fafc; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .footer-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .sig-box { text-align: center; width: 200px; float: right; }
        .sig-space { height: 50px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="brand-title">ROKET MINI MOTO BONDOWOSO</div>
                <div class="brand-sub">Laporan Analitik & Performa Bisnis Penjualan</div>
            </td>
            <td class="date-badge">
                <strong>TANGGAL CETAK</strong><br>
                {{ now()->format('d F Y, H:i') }} WIB
            </td>
        </tr>
    </table>

    {{-- KPI Cards --}}
    <table class="kpi-table" style="margin-left: -10px; margin-right: -10px;">
        <tr>
            <td class="kpi-cell highlight" style="width: 25%;">
                <div class="kpi-label">TOTAL OMZET DISETUJUI</div>
                <div class="kpi-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label" style="color:#64748b;">TRANSAKSI SUKSES</div>
                <div class="kpi-value" style="color:#0f172a;">{{ number_format($totalTransactions, 0, ',', '.') }} <span style="font-size:10px;font-weight:normal;">Laporan</span></div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label" style="color:#64748b;">PRODUK TERJUAL</div>
                <div class="kpi-value" style="color:#0f172a;">{{ number_format($totalItems, 0, ',', '.') }} <span style="font-size:10px;font-weight:normal;">Unit</span></div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label" style="color:#64748b;">RATA-RATA TRANSAKSI</div>
                <div class="kpi-value" style="color:#0f172a;">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Detail Kontribusi per Cabang --}}
    <div class="section-header">
        DETAIL KONTRIBUSI OMZET PER CABANG TOKO
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:30px;" class="text-center">No</th>
                <th>Nama Cabang</th>
                <th class="text-center">Volume Transaksi</th>
                <th class="text-right">Total Omzet Disetor</th>
                <th class="text-right">Kontribusi (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($storeOmzet as $index => $so)
            @php $pct = $totalOmzet > 0 ? round(($so->total / $totalOmzet) * 100, 1) : 0; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-weight:bold; color:#0f172a;">{{ $so->store->name ?? 'Cabang Tidak Diketahui' }}</td>
                <td class="text-center">{{ $so->count }} Laporan</td>
                <td class="text-right" style="font-weight:bold; color:#e63946;">Rp {{ number_format($so->total, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight:bold;">{{ $pct }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data kontribusi cabang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Distribusi Kategori --}}
    <div class="section-header">
        DISTRIBUSI PENJUALAN PER KATEGORI PRODUK
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:30px;" class="text-center">No</th>
                <th>Nama Kategori Produk</th>
                <th class="text-center">Total Volume Terjual</th>
                <th class="text-right">Total Nominal Omzet (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $catIdx = 1; @endphp
            @forelse($catSummary as $name => $data)
            <tr>
                <td class="text-center">{{ $catIdx++ }}</td>
                <td style="font-weight:bold; color:#0f172a;">{{ $name }}</td>
                <td class="text-center">{{ number_format($data['qty'], 0, ',', '.') }} Unit</td>
                <td class="text-right" style="font-weight:bold;">Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada data distribusi kategori.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="vertical-align: bottom;">
                <div style="color:#64748b; font-size:10px;">
                    Dokumen ini dihasilkan secara otomatis oleh Sistem Manajemen Roket Mini Moto.<br>
                    Bondowoso, Jawa Timur 68212
                </div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <div class="sig-box">
                    <div style="font-weight:bold; color:#0f172a; font-size:10px;">Disetujui Oleh,</div>
                    <div class="sig-space"></div>
                    <div style="font-weight:bold; color:#0f172a; text-decoration:underline;">( Management / Admin )</div>
                    <div style="color:#64748b; font-size:9px;">Roket Mini Moto</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
