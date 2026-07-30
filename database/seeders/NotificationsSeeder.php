<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        if (!$admin) return;

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
