<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Roket Mini Moto</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.5cm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        /* Header Document */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .brand-title {
            font-weight: 800;
            font-size: 18px;
            color: #e63946;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        .brand-sub {
            color: #64748b;
            font-size: 10px;
            margin: 0;
        }

        .doc-meta {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        .doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Filter Summary Box */
        .filter-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f1f5f9;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 10px;
        }

        .filter-table td {
            padding: 4px 8px;
        }

        /* Summary Metrics Grid */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 20px;
        }

        .summary-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            background-color: #ffffff;
        }

        .summary-box .label {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .summary-box .val {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        /* Data Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .report-table th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }

        .report-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-disetujui {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-diproses {
            background-color: #fef9c3;
            color: #a16207;
        }

        .status-ditolak {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Signature Footer */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .sig-box {
            text-align: center;
            width: 180px;
            float: right;
        }

        .sig-space {
            height: 50px;
        }

        .sig-name {
            font-weight: 700;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td>
                <div class="brand-title">ROKET MINI MOTO</div>
                <div class="brand-sub">Sistem Informasi Operasional & Manajemen Cabang</div>
            </td>
            <td class="doc-meta">
                <div style="font-weight:700; font-size:11px; color:#0f172a;">DOKUMEN LAPORAN RESMI</div>
                <div>Tanggal Cetak: {{ date('d/m/Y H:i') }} WIB</div>
                <div>Dicetak Oleh: {{ auth()->user()->name ?? 'System' }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">Laporan Penjualan Operasional</div>

    <!-- Filter Details -->
    <table class="filter-table">
        <tr>
            <td>Toko Cabang: <strong>{{ $selectedStore ? $selectedStore->name : 'Semua Toko Cabang' }}</strong></td>
            <td>Periode: <strong>{{ $periodLabel }}</strong></td>
            <td style="text-align: right;">Total Data: <strong>{{ $reports->count() }} Laporan</strong></td>
        </tr>
    </table>

    <!-- Summary Metric Boxes -->
    <table class="summary-table" style="margin-left: -10px; margin-right: -10px;">
        <tr>
            <td style="width: 33%;">
                <div class="summary-box">
                    <div class="label">Total Omzet Disetujui</div>
                    <div class="val" style="color:#16a34a;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33%;">
                <div class="summary-box">
                    <div class="label">Transaksi Valid</div>
                    <div class="val">{{ $approvedCount }} Transaksi</div>
                </div>
            </td>
            <td style="width: 33%;">
                <div class="summary-box">
                    <div class="label">Total Barang Terjual</div>
                    <div class="val">{{ number_format($totalItems, 0, ',', '.') }} Pcs</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 120px;">Kode / Tgl</th>
                <th>Toko Cabang</th>
                <th>Kasir / Petugas</th>
                <th style="text-align: center; width: 60px;">Item</th>
                <th style="text-align: right; width: 120px;">Total Penjualan</th>
                <th style="text-align: center; width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $r)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight:700;">#REP-{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size:9px; color:#64748b;">{{ \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y H:i') }}</div>
                </td>
                <td>
                    <div style="font-weight:600; color:#0f172a;">{{ $r->store->name ?? '-' }}</div>
                </td>
                <td>
                    <div>{{ $r->user->name ?? '-' }}</div>
                    <div style="font-size:9px; color:#64748b;">@ {{ $r->user->username ?? '' }}</div>
                </td>
                <td style="text-align: center; font-weight: 600;">
                    {{ $r->total_items }} pcs
                </td>
                <td style="text-align: right; font-weight: 700; color:#0f172a;">
                    Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                </td>
                <td style="text-align: center;">
                    <span class="status-badge status-{{ strtolower($r->status) }}">
                        {{ $r->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color:#94a3b8;">
                    Tidak ada data laporan yang sesuai dengan filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Info -->
    <div style="color:#64748b; font-size:9px; margin-top: 20px;">
        Dokumen ini di-generate secara otomatis oleh Sistem Roket Mini Moto. &nbsp;|&nbsp; Bondowoso, Jawa Timur 68212
    </div>

</body>
</html>
