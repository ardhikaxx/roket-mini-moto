<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\SalesReport;
use App\Models\SalesReportItem;
use App\Models\SalesReportImage;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ReportStatusHistory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Budi Administrator',
                'pin' => Hash::make('2222'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true
            ]
        );

        $kepala1 = User::firstOrCreate(['username' => 'kepala_sby'], ['name' => 'Agus Kepala SBY', 'pin' => Hash::make('2222'), 'role' => 'kepala_toko', 'phone' => '081234567891', 'is_active' => true]);
        $kepala2 = User::firstOrCreate(['username' => 'kepala_mlg'], ['name' => 'Dewi Kepala MLG', 'pin' => Hash::make('2222'), 'role' => 'kepala_toko', 'phone' => '081234567892', 'is_active' => true]);

        $karyawans = [];
        for ($i = 1; $i <= 8; $i++) {
            $karyawans[] = User::firstOrCreate(
                ['username' => "karyawan$i"],
                ['name' => "Karyawan Store $i", 'pin' => Hash::make('2222'), 'role' => 'karyawan', 'phone' => "0812000000$i", 'is_active' => true]
            );
        }

        // 2. Create Stores
        $stores = [
            Store::updateOrCreate(
                ['code' => 'RMM-01'],
                [
                    'name' => 'Roket Mini Moto 1',
                    'address' => 'Utara Hotel Baru, Jln. Kartini No.41, Pegadaian, Blindungan, Kec. Bondowoso, Jawa Timur 68212',
                    'phone' => '0823-3546-5000',
                    'operational_hours' => '08:00 - 22:00 WIB',
                    'is_active' => true
                ]
            ),
            Store::updateOrCreate(
                ['code' => 'RMM-02'],
                [
                    'name' => 'Roket Mini Moto 2',
                    'address' => 'Utara Hotel Baru, Jln. Kartini No.41, Pegadaian, Blindungan, Kec. Bondowoso, Jawa Timur 68212',
                    'phone' => '0823-3546-5000',
                    'operational_hours' => '08:00 - 22:00 WIB',
                    'is_active' => true
                ]
            ),
            Store::updateOrCreate(
                ['code' => 'RMM-03'],
                [
                    'name' => 'Roket Mini Moto 3',
                    'address' => 'Utara Hotel Baru, Jln. Kartini No.41, Pegadaian, Blindungan, Kec. Bondowoso, Jawa Timur 68212',
                    'phone' => '0823-3546-5000',
                    'operational_hours' => '08:00 - 22:00 WIB',
                    'is_active' => true
                ]
            ),
            Store::updateOrCreate(
                ['code' => 'RMM-04'],
                [
                    'name' => 'Roket Mini Moto 4',
                    'address' => 'Utara Hotel Baru, Jln. Kartini No.41, Pegadaian, Blindungan, Kec. Bondowoso, Jawa Timur 68212',
                    'phone' => '0823-3546-5000',
                    'operational_hours' => '08:00 - 22:00 WIB',
                    'is_active' => true
                ]
            ),
        ];

        // Assign stores to users
        $kepala1->stores()->sync([$stores[0]->id, $stores[1]->id]);
        $kepala2->stores()->sync([$stores[2]->id]);
        
        $karyawans[0]->stores()->sync([$stores[0]->id]);
        $karyawans[1]->stores()->sync([$stores[0]->id]);
        $karyawans[2]->stores()->sync([$stores[1]->id]);
        $karyawans[3]->stores()->sync([$stores[1]->id]);
        $karyawans[4]->stores()->sync([$stores[2]->id]);
        $karyawans[5]->stores()->sync([$stores[2]->id]);
        $karyawans[6]->stores()->sync([$stores[3]->id]);
        $karyawans[7]->stores()->sync([$stores[3]->id]);

        // 3. Create Categories
        $categoryNames = [
            'Mini Trail' => 'mini-trail', 'Mobil Aki' => 'mobil-aki', 'ATV Mini' => 'atv-mini',
            'Sepeda Listrik' => 'sepeda-listrik', 'Kembang Api' => 'kembang-api', 'Sparepart' => 'sparepart', 'Aksesoris' => 'aksesoris'
        ];
        $categoryModels = [];
        foreach ($categoryNames as $name => $slug) {
            $categoryModels[] = Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        // 4. Create Products
        $productData = [
            ['category_id' => $categoryModels[0]->id, 'sku' => 'MT-50CC-RED', 'name' => 'Mini Trail 50cc Red Edition', 'price' => 2500000, 'stock' => 15],
            ['category_id' => $categoryModels[0]->id, 'sku' => 'MT-50CC-BLU', 'name' => 'Mini Trail 50cc Blue Edition', 'price' => 2500000, 'stock' => 12],
            ['category_id' => $categoryModels[1]->id, 'sku' => 'MA-SUV-12V', 'name' => 'Mobil Aki SUV 12V Dual Motor', 'price' => 1850000, 'stock' => 20],
            ['category_id' => $categoryModels[1]->id, 'sku' => 'MA-SEDAN-6V', 'name' => 'Mobil Aki Sedan 6V Classic', 'price' => 1200000, 'stock' => 5],
            ['category_id' => $categoryModels[2]->id, 'sku' => 'ATV-49CC', 'name' => 'ATV Mini Adventure 49cc', 'price' => 3200000, 'stock' => 8],
            ['category_id' => $categoryModels[3]->id, 'sku' => 'EBIKE-A30', 'name' => 'Sepeda Listrik A30 Pro', 'price' => 4500000, 'stock' => 10],
            ['category_id' => $categoryModels[3]->id, 'sku' => 'EBIKE-C15', 'name' => 'Sepeda Listrik C15 City', 'price' => 3800000, 'stock' => 15],
            ['category_id' => $categoryModels[4]->id, 'sku' => 'KA-ROMAN-100', 'name' => 'Kembang Api Roman Candle 100s', 'price' => 150000, 'stock' => 50],
            ['category_id' => $categoryModels[4]->id, 'sku' => 'KA-CAKE-25', 'name' => 'Kembang Api Cake 25 Shots', 'price' => 250000, 'stock' => 30],
            ['category_id' => $categoryModels[5]->id, 'sku' => 'SP-TIRE-10', 'name' => 'Ban Dalam Ring 10', 'price' => 45000, 'stock' => 100],
            ['category_id' => $categoryModels[5]->id, 'sku' => 'SP-CARB-49', 'name' => 'Karburator Racing 49cc', 'price' => 185000, 'stock' => 25],
            ['category_id' => $categoryModels[6]->id, 'sku' => 'ACC-HELM-KIDS', 'name' => 'Helm Anak Motocross', 'price' => 250000, 'stock' => 40],
            ['category_id' => $categoryModels[6]->id, 'sku' => 'ACC-GLOVE', 'name' => 'Sarung Tangan Riding Anak', 'price' => 75000, 'stock' => 60],
        ];
        
        $productModels = [];
        foreach ($productData as $pd) {
            $productModels[] = Product::firstOrCreate(['sku' => $pd['sku']], array_merge($pd, [
                'description' => 'Deskripsi produk premium untuk ' . $pd['name'],
                'is_active' => true, 'show_on_landing' => true, 'unit' => 'pcs'
            ]));
        }

        // 5. Generate Realistic Sales Reports (Over the last 30 days)
        $statuses = ['disetujui', 'diproses', 'ditolak'];
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Generate around 150 reports
        for ($i = 0; $i < 150; $i++) {
            $randomDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
            $karyawan = $karyawans[array_rand($karyawans)];
            $store = $karyawan->stores->first();
            $status = $i < 130 ? 'disetujui' : ($i < 140 ? 'diproses' : 'ditolak');

            // Select 1 to 3 random products
            $numProducts = rand(1, 3);
            $selectedProducts = [];
            $keys = array_rand($productModels, $numProducts);
            if (!is_array($keys)) $keys = [$keys];
            
            foreach ($keys as $k) {
                $selectedProducts[] = $productModels[$k];
            }

            $totalAmount = 0;
            $totalItems = 0;
            $report = SalesReport::create([
                'user_id' => $karyawan->id,
                'store_id' => $store->id,
                'transaction_date' => $randomDate,
                'total_items' => 0, // Will update
                'total_amount' => 0, // Will update
                'status' => $status,
                'notes' => rand(0,1) ? 'Pembayaran via transfer.' : 'Customer ambil sendiri di toko.',
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            if ($status == 'ditolak') {
                $report->rejection_reason = 'Foto bukti kurang jelas / nominal tidak sesuai.';
                $report->save();
            }

            // Create items
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

            // Update total
            $report->update([
                'total_amount' => $totalAmount,
                'total_items' => $totalItems
            ]);

            // Add history
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

            // Add dummy image
            SalesReportImage::create([
                'sales_report_id' => $report->id,
                'image_path' => 'reports/dummy.jpg' // We just fake the DB entry
            ]);
        }

        // 6. Generate Recent Audit Logs
        for ($i = 0; $i < 20; $i++) {
            $user = $i % 3 == 0 ? $admin : $karyawans[array_rand($karyawans)];
            AuditLog::create([
                'user_id' => $user->id,
                'action' => ['login', 'create_report', 'approve_report', 'update_product'][rand(0, 3)],
                'model' => ['Auth', 'Report', 'Product'][rand(0, 2)],
                'description' => 'User melakukan aktivitas pada sistem.',
                'created_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);
        }

        // 8. Seed Sales Targets (2 bulan terakhir)
        $this->call(SalesTargetSeeder::class);

        // 9. Generate Notifications for Admin
        for ($i = 0; $i < 5; $i++) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Laporan Baru Masuk',
                'message' => 'Terdapat laporan penjualan baru dari Karyawan yang menunggu persetujuan.',
                'type' => 'report_submitted',
                'is_read' => rand(0, 1) == 1,
                'created_at' => Carbon::now()->subMinutes(rand(5, 300)),
            ]);
        }
    }
}
