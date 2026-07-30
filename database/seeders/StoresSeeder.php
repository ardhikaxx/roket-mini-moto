<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\User;

class StoresSeeder extends Seeder
{
    public function run(): void
    {
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

        $kepala1 = User::where('username', 'kepala_sby')->first();
        $kepala2 = User::where('username', 'kepala_mlg')->first();
        $karyawans = User::where('role', 'karyawan')->orderBy('id')->get();

        if ($kepala1) {
            $kepala1->stores()->sync([$stores[0]->id, $stores[1]->id]);
        }
        if ($kepala2) {
            $kepala2->stores()->sync([$stores[2]->id]);
        }

        $assignments = [0, 0, 1, 1, 2, 2, 3, 3];
        foreach ($karyawans as $idx => $k) {
            if (isset($assignments[$idx])) {
                $k->stores()->sync([$stores[$assignments[$idx]]->id]);
            }
        }
    }
}
