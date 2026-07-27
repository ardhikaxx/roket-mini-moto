<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Operasional - Roket Mini Moto</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1e293b; font-size: 10px; margin: 0; padding: 0; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border-bottom: 2px solid #e63946; padding-bottom: 8px; }
        .brand-title { font-size: 16px; font-weight: bold; color: #e63946; text-transform: uppercase; margin: 0; }
        .brand-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
        .doc-meta { text-align: right; font-size: 9px; color: #64748b; }

        .doc-title { font-size: 14px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin: 0 0 10px 0; letter-spacing: 0.5px; }

        .filter-table { width: 100%; border-collapse: collapse; background-color: #f1f5f9; padding: 8px 12px; border-radius: 4px; margin-bottom: 15px; font-size: 9.5px; }
        .filter-table td { padding: 4px 8px; }

        .summary-table { width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 15px; }
        .summary-cell { border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 12px; background-color: #ffffff; }
        .summary-label { font-size: 8.5px; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 2px; }
        .summary-val { font-size: 14px; font-weight: bold; color: #0f172a; }

        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .report-table th { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 9px; text-transform: uppercase; padding: 7px 8px; border-bottom: 1px solid #cbd5e1; text-align: left; }
        .report-table td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .report-table tr:nth-child(even) td { background-color: #f8fafc; }

        .status-badge { display: inline-block; padding: 2px 6px; border-radius: 8px; font-size: 8.5px; font-weight: bold; text-transform: uppercase; }
        .status-disetujui { background-color: #dcfce7; color: #15803d; }
        .status-diproses { background-color: #fef9c3; color: #a16207; }
        .status-ditolak { background-color: #fee2e2; color: #b91c1c; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .footer-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sig-box { text-align: center; width: 180px; float: right; }
        .sig-space { height: 45px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="brand-title">ROKET MINI MOTO BONDOWOSO</div>
                <div class="brand-sub">Laporan Rekapitulasi Transaksi Penjualan Operasional</div>
            </td>
            <td class="doc-meta">
                <strong style="color:#0f172a;">DOKUMEN RESMI LAPORAN</strong><br>
                Tanggal Cetak: {{ date('d/m/Y H:i') }} WIB<br>
                Dicetak Oleh: {{ auth()->user()->name ?? 'System' }}
            </td>
        </tr>
    </table>

    <div class="doc-title">LAPORAN PENJUALAN OPERASIONAL</div>

    {{-- Filter Details --}}
    <table class="filter-table">
        <tr>
            <td>Toko Cabang: <strong>{{ $selectedStore ? $selectedStore->name : 'Semua Toko Cabang' }}</strong></td>
            <td>Periode Transaksi: <strong>{{ $periodLabel }}</strong></td>
            <td style="text-align: right;">Total Data: <strong>{{ $reports->count() }} Berkas Laporan</strong></td>
        </tr>
    </table>

    {{-- Summary Metric Boxes --}}
    <table class="summary-table" style="margin-left: -8px; margin-right: -8px;">
        <tr>
            <td class="summary-cell" style="width: 33%;">
                <div class="summary-label">TOTAL OMZET DISETUJUI</div>
                <div class="summary-val" style="color:#16a34a;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
            </td>
            <td class="summary-cell" style="width: 33%;">
                <div class="summary-label">TRANSAKSI VALID</div>
                <div class="summary-val">{{ $approvedCount }} Transaksi</div>
            </td>
            <td class="summary-cell" style="width: 33%;">
                <div class="summary-label">TOTAL BARANG TERJUAL</div>
                <div class="summary-val">{{ number_format($totalItems, 0, ',', '.') }} Unit</div>
            </td>
        </tr>
    </table>

    {{-- Main Data Table --}}
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 100px;">Kode / Tgl</th>
                <th>Toko Cabang</th>
                <th>Kasir / Petugas</th>
                <th class="text-center" style="width: 60px;">Item</th>
                <th class="text-right" style="width: 110px;">Total Penjualan</th>
                <th class="text-center" style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $r)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight:bold;">#REP-{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size:8.5px; color:#64748b;">{{ \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y H:i') }}</div>
                </td>
                <td style="font-weight:bold; color:#0f172a;">{{ $r->store->name ?? '-' }}</td>
                <td>
                    <div>{{ $r->user->name ?? '-' }}</div>
                    <div style="font-size:8.5px; color:#64748b;">@ {{ $r->user->username ?? '' }}</div>
                </td>
                <td class="text-center" style="font-weight: bold;">
                    {{ $r->total_items }} Unit
                </td>
                <td class="text-right" style="font-weight: bold; color:#0f172a;">
                    Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    <span class="status-badge status-{{ strtolower($r->status) }}">
                        {{ $r->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color:#94a3b8;">
                    Tidak ada data laporan yang sesuai dengan filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="vertical-align: bottom;">
                <div style="color:#64748b; font-size:8.5px;">
                    Dokumen ini di-generate otomatis oleh Sistem Operasional Roket Mini Moto.<br>
                    Kantor Pusat: Jln. Kartini No.41, Blindungan, Bondowoso
                </div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <div class="sig-box">
                    <div style="font-weight:bold; color:#0f172a; font-size:9.5px;">Disetujui Oleh,</div>
                    <div class="sig-space"></div>
                    <div style="font-weight:bold; color:#0f172a; text-decoration:underline;">( Management / Admin )</div>
                    <div style="color:#64748b; font-size:8.5px;">Roket Mini Moto</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
