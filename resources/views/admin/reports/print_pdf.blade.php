<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Roket Mini Moto</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e63946;
            --dark: #0f172a;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            margin: 0;
            padding: 24px;
            font-size: 13px;
        }

        /* Printable Sheet */
        .sheet {
            background: #ffffff;
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        /* Top Action Bar (hidden when printing) */
        .action-bar {
            max-width: 1000px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background-color: var(--primary);
            color: #ffffff;
        }

        .btn-print:hover {
            background-color: #c42b37;
        }

        .btn-close {
            background-color: #e2e8f0;
            color: #475569;
        }

        .btn-close:hover {
            background-color: #cbd5e1;
        }

        /* Header Document */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        .brand-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 22px;
            color: var(--primary);
            margin: 0 0 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-sub {
            color: #64748b;
            font-size: 12px;
            margin: 0;
        }

        .doc-meta {
            text-align: right;
            font-size: 12px;
            color: #64748b;
        }

        .doc-title {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Filter Summary Badge */
        .filter-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            background-color: #f1f5f9;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 12px;
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-item strong {
            color: #334155;
        }

        /* Summary Metrics Box */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .summary-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            background-color: #ffffff;
        }

        .summary-box .label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .summary-box .val {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        /* Data Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .report-table th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            padding: 10px 12px;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }

        .report-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
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
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .sig-box {
            text-align: center;
            width: 200px;
        }

        .sig-space {
            height: 70px;
        }

        .sig-name {
            font-weight: 700;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            color: #0f172a;
        }

        /* Print Media CSS Overrides */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .sheet {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .action-bar {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="action-bar">
        <button onclick="window.history.back()" class="btn-action btn-close">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Printable Document Sheet -->
    <div class="sheet">
        
        <!-- Header -->
        <div class="doc-header" style="justify-content: flex-end;">
            <div class="doc-meta">
                <div style="font-weight:700; font-size:14px; color:#0f172a;">DOKUMEN LAPORAN</div>
                <div>Tanggal Cetak: {{ date('d/m/Y H:i') }}</div>
                <div>Dicetak Oleh: {{ auth()->user()->name }}</div>
            </div>
        </div>

        <h2 class="doc-title">Laporan Penjualan Operasional</h2>

        <!-- Filter Details -->
        <div class="filter-badges">
            <div class="filter-item">
                <i class="fa-solid fa-store text-primary"></i>
                <span>Toko: <strong>{{ $selectedStore ? $selectedStore->name : 'Semua Toko Cabang' }}</strong></span>
            </div>
            <div class="filter-item">
                <i class="fa-regular fa-calendar-days text-primary"></i>
                <span>Periode: <strong>{{ $periodLabel }}</strong></span>
            </div>
            <div class="filter-item">
                <i class="fa-solid fa-layer-group text-primary"></i>
                <span>Total Data: <strong>{{ $reports->count() }} Laporan</strong></span>
            </div>
        </div>

        <!-- Summary Metric Boxes -->
        <div class="summary-grid">
            <div class="summary-box">
                <div class="label">Total Omzet Disetujui</div>
                <div class="val" style="color:#16a34a;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Transaksi Valid</div>
                <div class="val">{{ $approvedCount }} Transaksi</div>
            </div>
            <div class="summary-box">
                <div class="label">Total Barang Terjual</div>
                <div class="val">{{ number_format($totalItems, 0, ',', '.') }} Pcs</div>
            </div>
        </div>

        <!-- Main Data Table -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Kode / Tgl</th>
                    <th>Toko Cabang</th>
                    <th>Kasir / Petugas</th>
                    <th style="text-align: center;">Item</th>
                    <th style="text-align: right;">Total Penjualan</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight:700;">#REP-{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div style="font-size:11px; color:#64748b;">{{ \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $r->store->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div>{{ $r->user->name ?? '-' }}</div>
                        <div style="font-size:11px; color:#64748b;">@ {{ $r->user->username ?? '' }}</div>
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
                    <td colspan="7" style="text-align: center; padding: 30px; color:#94a3b8;">
                        Tidak ada data laporan yang sesuai dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>



    </div>

    <script>
        // Auto trigger print on page load if opened in new tab/window
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
