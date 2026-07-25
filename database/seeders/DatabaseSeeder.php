<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'pin' => Hash::make('1234'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true
        ]);

        $store = Store::create([
            'code' => 'BDW-01',
            'name' => 'Toko Pusat Bondowoso',
            'address' => 'Jl. Kartini No. 41',
            'phone' => '082335465000',
            'is_active' => true
        ]);

        Category::create(['name' => 'Mini Trail', 'slug' => 'mini-trail']);
        Category::create(['name' => 'Mobil Aki', 'slug' => 'mobil-aki']);
        Category::create(['name' => 'ATV Mini', 'slug' => 'atv-mini']);

        \App\Models\Product::create([
            'category_id' => 1,
            'sku' => 'MT-RACE-50',
            'name' => 'Mini Trail Race Edition 50cc',
            'price' => 2500000,
            'description' => 'Desain sporty, mesin bertenaga, sangat cocok untuk pemula yang menyukai tantangan.',
            'is_active' => true,
            'show_on_landing' => true
        ]);
        
        \App\Models\Product::create([
            'category_id' => 2,
            'sku' => 'MA-SUV-12V',
            'name' => 'Mobil Aki Off-Road Kids 12V',
            'price' => 1850000,
            'description' => 'Mobil aki berdesain SUV gagah dengan lampu LED, remote control, dan suspensi nyaman.',
            'is_active' => true,
            'show_on_landing' => true
        ]);
        
        \App\Models\Product::create([
            'category_id' => 3,
            'sku' => 'ATV-ADV-49',
            'name' => 'ATV Mini Adventure 49cc',
            'price' => 3200000,
            'description' => 'Kendaraan roda empat mini yang stabil, aman untuk anak, dan mampu melibas medan ringan.',
            'is_active' => true,
            'show_on_landing' => true
        ]);
        
        $kepala = User::create([
            'name' => 'Kepala Toko',
            'username' => 'kepala',
            'pin' => Hash::make('1234'),
            'role' => 'kepala_toko',
            'is_active' => true
        ]);
        $kepala->stores()->attach($store->id);
        
        $karyawan = User::create([
            'name' => 'Karyawan 1',
            'username' => 'karyawan',
            'pin' => Hash::make('1234'),
            'role' => 'karyawan',
            'is_active' => true
        ]);
        $karyawan->stores()->attach($store->id);
    }
}
