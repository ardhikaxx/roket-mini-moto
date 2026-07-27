<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Laporan Penjualan</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
    body { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; font-size: 11pt; }
    .title-banner { background-color: #e63946; color: #ffffff; font-size: 16pt; font-weight: bold; text-align: center; height: 40px; vertical-align: middle; }
    .subtitle-banner { background-color: #1e293b; color: #ffffff; font-size: 11pt; font-weight: bold; text-align: center; height: 25px; vertical-align: middle; }
    .label-cell { font-weight: bold; color: #334155; }
    .th-header { background-color: #0f172a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000; height: 30px; vertical-align: middle; }
    .td-cell { border: 1px solid #cbd5e1; vertical-align: middle; padding: 6px; }
    .td-even { background-color: #f8fafc; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-bold { font-weight: bold; }
    .status-disetujui { background-color: #dcfce7; color: #15803d; font-weight: bold; text-align: center; }
    .status-diproses { background-color: #fef9c3; color: #a16207; font-weight: bold; text-align: center; }
    .status-ditolak { background-color: #fee2e2; color: #b91c1c; font-weight: bold; text-align: center; }
    .summary-card { background-color: #f1f5f9; border: 1px solid #cbd5e1; font-weight: bold; }
    .total-row td { background-color: #dcfce7; border: 2px solid #16a34a; font-weight: bold; font-size: 12pt; height: 35px; }
</style>
</head>
<body>

<table>
    <!-- Title Banner -->
    <tr>
        <td colspan="9" class="title-banner">ROKET MINI MOTO BONDOWOSO</td>
    </tr>
    <tr>
        <td colspan="9" class="subtitle-banner">LAPORAN PENJUALAN OPERASIONAL & OMZET TOKO</td>
    </tr>
    <tr><td colspan="9"></td></tr>

    <!-- Metadata Information -->
    <tr>
        <td class="label-cell">Cabang Toko:</td>
        <td colspan="3">{{ $selectedStore ? $selectedStore->name : 'Semua Toko Cabang' }}</td>
        <td></td>
        <td class="label-cell">Tanggal Ekspor:</td>
        <td colspan="3">{{ date('d/m/Y H:i') }}</td>
    </tr>
    <tr>
        <td class="label-cell">Periode Waktu:</td>
        <td colspan="3">{{ $periodLabel }}</td>
        <td></td>
        <td class="label-cell">Diekspor Oleh:</td>
        <td colspan="3">{{ auth()->user()->name }} ({{ auth()->user()->role }})</td>
    </tr>
    <tr><td colspan="9"></td></tr>

    <!-- Summary Box Table -->
    <tr>
        <td colspan="3" class="summary-card text-center" style="background-color: #dcfce7; color: #15803d; height: 30px;">
            TOTAL OMZET DISETUJUI: Rp {{ number_format($totalOmzet, 0, ',', '.') }}
        </td>
        <td colspan="3" class="summary-card text-center" style="background-color: #e0f2fe; color: #0369a1;">
            TOTAL TRANSAKSI VALID: {{ $approvedCount }} Transaksi
        </td>
        <td colspan="3" class="summary-card text-center" style="background-color: #fef3c7; color: #b45309;">
            TOTAL BARANG TERJUAL: {{ number_format($totalItems, 0, ',', '.') }} Pcs
        </td>
    </tr>
    <tr><td colspan="9"></td></tr>

    <!-- Main Data Table Headers -->
    <thead>
        <tr>
            <th class="th-header" style="width: 40px;">No</th>
            <th class="th-header" style="width: 120px;">ID Laporan</th>
            <th class="th-header" style="width: 150px;">Tanggal Transaksi</th>
            <th class="th-header" style="width: 180px;">Cabang Toko</th>
            <th class="th-header" style="width: 160px;">Kasir / Petugas</th>
            <th class="th-header" style="width: 250px;">Rincian Produk Terjual</th>
            <th class="th-header" style="width: 100px;">Total Item</th>
            <th class="th-header" style="width: 160px;">Total Penjualan (Rp)</th>
            <th class="th-header" style="width: 120px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reports as $index => $r)
        @php
            $bgClass = $index % 2 == 0 ? '' : 'td-even';
            $itemsSummary = [];
            foreach($r->items as $item) {
                $itemsSummary[] = $item->quantity . 'x ' . ($item->product_name ?? 'Produk');
            }
            $itemsText = implode(', ', $itemsSummary);
        @endphp
        <tr>
            <td class="td-cell text-center {{ $bgClass }}">{{ $index + 1 }}</td>
            <td class="td-cell text-center text-bold {{ $bgClass }}">#REP-{{ str_pad($r->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="td-cell text-center {{ $bgClass }}">{{ \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y H:i') }}</td>
            <td class="td-cell {{ $bgClass }}">{{ $r->store->name ?? '-' }}</td>
            <td class="td-cell {{ $bgClass }}">{{ $r->user->name ?? '-' }}</td>
            <td class="td-cell {{ $bgClass }}">{{ $itemsText ?: '-' }}</td>
            <td class="td-cell text-center text-bold {{ $bgClass }}">{{ $r->total_items }}</td>
            <td class="td-cell text-right text-bold {{ $bgClass }}" style="mso-number-format:'\#\,\#\#0';">
                Rp {{ number_format($r->total_amount, 0, ',', '.') }}
            </td>
            <td class="td-cell status-{{ strtolower($r->status) }}">
                {{ strtoupper($r->status) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="td-cell text-center" style="padding: 20px; color: #94a3b8;">
                Tidak ada data laporan penjualan yang sesuai dengan filter.
            </td>
        </tr>
        @endforelse

        <!-- Total Summary Row -->
        <tr class="total-row">
            <td colspan="6" class="text-right text-bold" style="padding-right: 15px;">TOTAL OMZET VALID (DISETUJUI):</td>
            <td class="text-center text-bold">{{ number_format($totalItems, 0, ',', '.') }}</td>
            <td class="text-right text-bold" style="color: #15803d; mso-number-format:'\#\,\#\#0';">
                Rp {{ number_format($totalOmzet, 0, ',', '.') }}
            </td>
            <td></td>
        </tr>
    </tbody>
</table>

</body>
</html>
