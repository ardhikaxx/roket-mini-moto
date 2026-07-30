<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\SalesReport;
use App\Models\SalesReportItem;
use App\Models\SalesReportImage;
use App\Models\ReportStatusHistory;
use Carbon\Carbon;

class SalesReportsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $karyawans = User::where('role', 'karyawan')->get();
        $productModels = Product::all();

        if ($karyawans->isEmpty() || $productModels->isEmpty()) {
            return;
        }

        $statuses = ['disetujui', 'diproses', 'ditolak'];
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        for ($i = 0; $i < 150; $i++) {
            $randomDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
            $karyawan = $karyawans->random();
            $store = $karyawan->stores->first();
            $status = $i < 130 ? 'disetujui' : ($i < 140 ? 'diproses' : 'ditolak');

            $numProducts = rand(1, 3);
            $selectedProducts = $productModels->random($numProducts);

            $totalAmount = 0;
            $totalItems = 0;

            $report = SalesReport::create([
                'user_id' => $karyawan->id,
                'store_id' => $store->id,
                'transaction_date' => $randomDate,
                'total_items' => 0,
                'total_amount' => 0,
                'status' => $status,
                'notes' => rand(0, 1) ? 'Pembayaran via transfer.' : 'Customer ambil sendiri di toko.',
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            if ($status == 'ditolak') {
                $report->rejection_reason = 'Foto bukti kurang jelas / nominal tidak sesuai.';
                $report->save();
            }

            foreach ($selectedProducts as $sp) {
                $qty = rand(1, 2);
                $subtotal = $sp->price * $qty;
                $totalAmount += $subtotal;
                $totalItems += $qty;

                SalesReportItem::create([
                    'sales_report_id' => $report->id,
                    'product_id' => $sp->id,
                    'product_name' => $sp->name,
                    'quantity' => $qty,
                    'price' => $sp->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $report->update([
                'total_amount' => $totalAmount,
                'total_items' => $totalItems
            ]);

            ReportStatusHistory::create([
                'sales_report_id' => $report->id,
                'from_status' => null,
                'to_status' => 'diproses',
                'user_id' => $karyawan->id,
                'notes' => 'Laporan dibuat.',
                'created_at' => $randomDate,
            ]);

            if ($status != 'diproses') {
                $actionDate = (clone $randomDate)->addHours(rand(1, 5));
                ReportStatusHistory::create([
                    'sales_report_id' => $report->id,
                    'from_status' => 'diproses',
                    'to_status' => $status,
                    'user_id' => $admin->id,
                    'notes' => $status == 'ditolak' ? 'Ditolak: Foto buram' : 'Laporan disetujui.',
                    'created_at' => $actionDate,
                ]);
            }

            SalesReportImage::create([
                'sales_report_id' => $report->id,
                'image_path' => 'reports/dummy.jpg'
            ]);
        }
    }
}
