<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::orderBy('id')->get();

        $productData = [
            ['category_id' => $categories[0]->id, 'sku' => 'MT-50CC-RED', 'name' => 'Mini Trail 50cc Red Edition', 'price' => 2500000, 'cost_price' => 1700000, 'stock' => 15],
            ['category_id' => $categories[0]->id, 'sku' => 'MT-50CC-BLU', 'name' => 'Mini Trail 50cc Blue Edition', 'price' => 2500000, 'cost_price' => 1700000, 'stock' => 12],
            ['category_id' => $categories[1]->id, 'sku' => 'MA-SUV-12V', 'name' => 'Mobil Aki SUV 12V Dual Motor', 'price' => 1850000, 'cost_price' => 1200000, 'stock' => 20],
            ['category_id' => $categories[1]->id, 'sku' => 'MA-SEDAN-6V', 'name' => 'Mobil Aki Sedan 6V Classic', 'price' => 1200000, 'cost_price' => 750000, 'stock' => 5],
            ['category_id' => $categories[2]->id, 'sku' => 'ATV-49CC', 'name' => 'ATV Mini Adventure 49cc', 'price' => 3200000, 'cost_price' => 2200000, 'stock' => 8],
            ['category_id' => $categories[3]->id, 'sku' => 'EBIKE-A30', 'name' => 'Sepeda Listrik A30 Pro', 'price' => 4500000, 'cost_price' => 3100000, 'stock' => 10],
            ['category_id' => $categories[3]->id, 'sku' => 'EBIKE-C15', 'name' => 'Sepeda Listrik C15 City', 'price' => 3800000, 'cost_price' => 2600000, 'stock' => 15],
            ['category_id' => $categories[4]->id, 'sku' => 'KA-ROMAN-100', 'name' => 'Kembang Api Roman Candle 100s', 'price' => 150000, 'cost_price' => 90000, 'stock' => 50],
            ['category_id' => $categories[4]->id, 'sku' => 'KA-CAKE-25', 'name' => 'Kembang Api Cake 25 Shots', 'price' => 250000, 'cost_price' => 160000, 'stock' => 30],
            ['category_id' => $categories[5]->id, 'sku' => 'SP-TIRE-10', 'name' => 'Ban Dalam Ring 10', 'price' => 45000, 'cost_price' => 25000, 'stock' => 100],
            ['category_id' => $categories[5]->id, 'sku' => 'SP-CARB-49', 'name' => 'Karburator Racing 49cc', 'price' => 185000, 'cost_price' => 110000, 'stock' => 25],
            ['category_id' => $categories[6]->id, 'sku' => 'ACC-HELM-KIDS', 'name' => 'Helm Anak Motocross', 'price' => 250000, 'cost_price' => 160000, 'stock' => 40],
            ['category_id' => $categories[6]->id, 'sku' => 'ACC-GLOVE', 'name' => 'Sarung Tangan Riding Anak', 'price' => 75000, 'cost_price' => 45000, 'stock' => 60],
        ];

        foreach ($productData as $pd) {
            Product::firstOrCreate(['sku' => $pd['sku']], array_merge($pd, [
                'description' => 'Deskripsi produk premium untuk ' . $pd['name'],
                'is_active' => true, 'show_on_landing' => true, 'unit' => 'pcs'
            ]));
        }
    }
}
