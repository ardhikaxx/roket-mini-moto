<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - Roket Mini Moto</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #0f172a; font-size: 10px; margin: 0; padding: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; border-bottom: 2px solid #e63946; padding-bottom: 8px; }
        .brand-title { font-size: 16px; font-weight: bold; color: #e63946; text-transform: uppercase; margin: 0; }
        .brand-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
        .date-badge { text-align: right; font-size: 9px; color: #475569; }
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 16px; }
        .kpi-cell { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px; vertical-align: top; }
        .kpi-cell.highlight { background: #059669; color: #ffffff; border: none; }
        .kpi-label { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; margin-bottom: 3px; }
        .kpi-value { font-size: 14px; font-weight: bold; }
        .section-header { font-size: 11px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 6px; border-left: 3px solid #e63946; padding-left: 8px; margin-top: 16px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { background-color: #0f172a; color: #ffffff; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; font-weight: bold; }
        .data-table td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; color: #334155; font-size: 9px; }
        .data-table tr:nth-child(even) td { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="brand-title">ROKET MINI MOTO BONDOWOSO</div>
                <div class="brand-sub">Laporan Laba Rugi Penjualan</div>
            </td>
            <td class="date-badge">
                <strong>PERIODE</strong><br>
                {{ $periodLabel }}<br>
                Cetak: {{ now()->format('d F Y, H:i') }} WIB
            </td>
        </tr>
    </table>

    <table class="kpi-table" style="margin-left:-8px;margin-right:-8px;">
        <tr>
            <td class="kpi-cell" style="width:25%;">
                <div class="kpi-label">Total Omzet</div>
                <div class="kpi-value">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
            </td>
            <td class="kpi-cell" style="width:25%;">
                <div class="kpi-label" style="color:#64748b;">Total Modal</div>
                <div class="kpi-value" style="color:#dc2626;">Rp {{ number_format($totalCost,0,',','.') }}</div>
            </td>
            <td class="kpi-cell highlight" style="width:25%;">
                <div class="kpi-label" style="opacity:0.9;">Laba Kotor</div>
                <div class="kpi-value">Rp {{ number_format($totalProfit,0,',','.') }}</div>
            </td>
            <td class="kpi-cell" style="width:25%;">
                <div class="kpi-label" style="color:#64748b;">Margin</div>
                <div class="kpi-value" style="color:#8e44ad;">{{ $totalProfitPercent }}%</div>
            </td>
        </tr>
    </table>

    <div class="section-header">RINCIAN PER TRANSAKSI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:20px;" class="text-center">#</th>
                <th>Tanggal</th>
                <th>Toko</th>
                <th>Kasir</th>
                <th class="text-right">Omzet</th>
                <th class="text-right">Modal</th>
                <th class="text-right">Laba</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportProfits as $i => $rp)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $rp['report']->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $rp['report']->store->name ?? '-' }}</td>
                <td>{{ $rp['report']->user->name ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($rp['revenue'],0,',','.') }}</td>
                <td class="text-right">Rp {{ number_format($rp['cost'],0,',','.') }}</td>
                <td class="text-right {{ $rp['profit'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($rp['profit'],0,',','.') }}</td>
                <td class="text-right">{{ $rp['percent'] }}%</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-header">RINCIAN PER PRODUK</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:20px;" class="text-center">#</th>
                <th>Produk</th>
                <th>SKU</th>
                <th class="text-center">Terjual</th>
                <th class="text-right">Omzet</th>
                <th class="text-right">Modal</th>
                <th class="text-right">Laba</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productProfits as $i => $pp)
            @php
                $profit = $pp['revenue'] - $pp['cost'];
                $percent = $pp['revenue'] > 0 ? round(($profit / $pp['revenue']) * 100, 1) : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $pp['product_name'] }}</td>
                <td>{{ $pp['product']->sku ?? '-' }}</td>
                <td class="text-center">{{ $pp['qty'] }} pcs</td>
                <td class="text-right">Rp {{ number_format($pp['revenue'],0,',','.') }}</td>
                <td class="text-right">Rp {{ number_format($pp['cost'],0,',','.') }}</td>
                <td class="text-right {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($profit,0,',','.') }}</td>
                <td class="text-right">{{ $percent }}%</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="color:#64748b; font-size:8px; margin-top:20px;">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Manajemen Roket Mini Moto. Bondowoso, Jawa Timur 68212
    </div>
</body>
</html>