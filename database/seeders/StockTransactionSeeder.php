<?php
namespace Database\Seeders;
use App\Models\{StockTransaction, Product, Store, SalesReport, User};
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StockTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $stores = Store::where('is_active', true)->get();
        $products = Product::all()->keyBy('id');

        $now = Carbon::now();
        $initialStock = [
            1 => ['qty' => 30, 'desc' => 'Stok awal Mini Trail 50cc Red dari supplier PT. Citra Motor'],
            2 => ['qty' => 25, 'desc' => 'Stok awal Mini Trail 50cc Blue dari supplier PT. Citra Motor'],
            3 => ['qty' => 35, 'desc' => 'Stok awal Mobil Aki SUV 12V dari supplier CV. Maju Jaya'],
            4 => ['qty' => 15, 'desc' => 'Stok awal Mobil Aki Sedan 6V dari supplier CV. Maju Jaya'],
            5 => ['qty' => 20, 'desc' => 'Stok awal ATV Mini Adventure dari PT. Bintang Terang'],
            6 => ['qty' => 18, 'desc' => 'Stok awal Sepeda Listrik A30 Pro dari supplier E-Bike Indonesia'],
            7 => ['qty' => 25, 'desc' => 'Stok awal Sepeda Listrik C15 City dari supplier E-Bike Indonesia'],
            8 => ['qty' => 80, 'desc' => 'Stok awal Kembang Api Roman Candle dari PT. Purna Raya'],
            9 => ['qty' => 60, 'desc' => 'Stok awal Kembang Api Cake 25 Shots dari PT. Purna Raya'],
            10 => ['qty' => 200, 'desc' => 'Stok awal Ban Dalam Ring 10 dari supplier sparepart'],
            11 => ['qty' => 50, 'desc' => 'Stok awal Karburator Racing dari supplier sparepart'],
            12 => ['qty' => 60, 'desc' => 'Stok awal Helm Anak Motocross dari distributor aksesoris'],
            13 => ['qty' => 100, 'desc' => 'Stok awal Sarung Tangan Riding Anak dari distributor aksesoris'],
        ];

        foreach ($initialStock as $pid => $data) {
            $product = $products->get($pid);
            if (!$product) continue;

            $stockAfter = $data['qty'];
            StockTransaction::create([
                'product_id' => $pid,
                'store_id' => $stores[0]->id,
                'user_id' => $admin->id,
                'type' => 'in',
                'quantity' => $data['qty'],
                'stock_before' => 0,
                'stock_after' => $stockAfter,
                'reference_type' => 'manual',
                'notes' => $data['desc'],
                'created_at' => $now->copy()->subDays(60),
            ]);
        }

        $restockTransactions = [
            ['pid' => 4, 'qty' => 5, 'day' => 45, 'note' => 'Restok Mobil Aki Sedan 6V (stok menipis)'],
            ['pid' => 5, 'qty' => 5, 'day' => 40, 'note' => 'Restok ATV Mini Adventure (pesanan customer)'],
            ['pid' => 8, 'qty' => 40, 'day' => 35, 'note' => 'Restok Kembang Api Roman Candle (musim liburan)'],
            ['pid' => 10, 'qty' => 50, 'day' => 30, 'note' => 'Restok Ban Dalam Ring 10 (stok gudang)'],
            ['pid' => 11, 'qty' => 15, 'day' => 25, 'note' => 'Restok Karburator Racing (fast moving)'],
            ['pid' => 13, 'qty' => 30, 'day' => 20, 'note' => 'Restok Sarung Tangan Riding Anak'],
        ];

        foreach ($restockTransactions as $rt) {
            $product = $products->get($rt['pid']);
            if (!$product) continue;

            $latestTx = StockTransaction::where('product_id', $rt['pid'])->latest()->first();
            $stockBefore = $latestTx ? $latestTx->stock_after : $product->stock;
            $stockAfter = $stockBefore + $rt['qty'];

            StockTransaction::create([
                'product_id' => $rt['pid'],
                'store_id' => $stores[array_rand($stores->toArray())]->id,
                'user_id' => $admin->id,
                'type' => 'in',
                'quantity' => $rt['qty'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => 'manual',
                'notes' => $rt['note'],
                'created_at' => $now->copy()->subDays($rt['day']),
            ]);
        }

        $stockOutTransactions = [
            ['pid' => 12, 'qty' => 5, 'day' => 15, 'note' => 'Stok keluar untuk display toko cabang 2'],
            ['pid' => 10, 'qty' => 10, 'day' => 12, 'note' => 'Pemindahan stok ke gudang penyimpanan'],
        ];

        foreach ($stockOutTransactions as $so) {
            $product = $products->get($so['pid']);
            if (!$product) continue;

            $latestTx = StockTransaction::where('product_id', $so['pid'])->latest()->first();
            $stockBefore = $latestTx ? $latestTx->stock_after : $product->stock;
            $stockAfter = $stockBefore - $so['qty'];

            StockTransaction::create([
                'product_id' => $so['pid'],
                'store_id' => $stores[1]->id,
                'user_id' => $admin->id,
                'type' => 'out',
                'quantity' => $so['qty'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => 'manual',
                'notes' => $so['note'],
                'created_at' => $now->copy()->subDays($so['day']),
            ]);
        }

        $transfers = [
            ['pid' => 3, 'from' => 0, 'to' => 1, 'qty' => 3, 'day' => 25, 'note' => 'Transfer untuk permintaan cabang 2'],
            ['pid' => 8, 'from' => 0, 'to' => 2, 'qty' => 15, 'day' => 18, 'note' => 'Transfer stok kembang api untuk event cabang 3'],
            ['pid' => 12, 'from' => 1, 'to' => 3, 'qty' => 5, 'day' => 10, 'note' => 'Transfer helm anak ke cabang 4'],
        ];

        foreach ($transfers as $tr) {
            $product = $products->get($tr['pid']);
            if (!$product) continue;

            StockTransaction::create([
                'product_id' => $tr['pid'],
                'store_id' => $stores[$tr['from']]->id,
                'user_id' => $admin->id,
                'type' => 'transfer_out',
                'quantity' => $tr['qty'],
                'stock_before' => $product->stock,
                'stock_after' => $product->stock,
                'reference_type' => 'transfer',
                'notes' => 'Transfer ke ' . $stores[$tr['to']]->name . ': ' . $tr['note'],
                'created_at' => $now->copy()->subDays($tr['day']),
            ]);

            StockTransaction::create([
                'product_id' => $tr['pid'],
                'store_id' => $stores[$tr['to']]->id,
                'user_id' => $admin->id,
                'type' => 'transfer_in',
                'quantity' => $tr['qty'],
                'stock_before' => 0,
                'stock_after' => $tr['qty'],
                'reference_type' => 'transfer',
                'notes' => 'Transfer dari ' . $stores[$tr['from']]->name . ': ' . $tr['note'],
                'created_at' => $now->copy()->subDays($tr['day']),
            ]);
        }

        $approvedReports = SalesReport::where('status', 'disetujui')->with('items.product')->get();
        $count = 0;
        foreach ($approvedReports as $report) {
            foreach ($report->items as $item) {
                if (!$item->product) continue;
                $count++;
                if ($count > 50) break 2;

                StockTransaction::create([
                    'product_id' => $item->product_id,
                    'store_id' => $report->store_id,
                    'user_id' => $report->user_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'stock_before' => $item->product->stock + $item->quantity,
                    'stock_after' => $item->product->stock,
                    'reference_type' => 'sales_report',
                    'reference_id' => $report->id,
                    'notes' => 'Penjualan via laporan #' . $report->id . ' (' . $item->quantity . 'x ' . ($item->product_name ?? $item->product->name) . ')',
                    'created_at' => $report->transaction_date,
                ]);
            }
        }

        $this->command->info('Stock transactions seeded: ' . StockTransaction::count() . ' transactions created.');
    }
}
