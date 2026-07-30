<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categoryNames = [
            'Mini Trail' => 'mini-trail', 'Mobil Aki' => 'mobil-aki', 'ATV Mini' => 'atv-mini',
            'Sepeda Listrik' => 'sepeda-listrik', 'Kembang Api' => 'kembang-api',
            'Sparepart' => 'sparepart', 'Aksesoris' => 'aksesoris'
        ];

        foreach ($categoryNames as $name => $slug) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
