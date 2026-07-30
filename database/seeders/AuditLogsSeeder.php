<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $karyawans = User::where('role', 'karyawan')->get();

        for ($i = 0; $i < 20; $i++) {
            $user = $i % 3 == 0 ? $admin : $karyawans->random();
            AuditLog::create([
                'user_id' => $user->id,
                'action' => ['login', 'create_report', 'approve_report', 'update_product'][rand(0, 3)],
                'model' => ['Auth', 'Report', 'Product'][rand(0, 2)],
                'description' => 'User melakukan aktivitas pada sistem.',
                'created_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);
        }
    }
}
