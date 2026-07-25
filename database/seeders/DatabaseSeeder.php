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
