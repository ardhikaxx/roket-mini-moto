<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            StoresSeeder::class,
            CategoriesSeeder::class,
            ProductsSeeder::class,
            SalesReportsSeeder::class,
            AuditLogsSeeder::class,
            NotificationsSeeder::class,
            StockTransactionSeeder::class,
            SalesTargetSeeder::class,
        ]);
    }
}
