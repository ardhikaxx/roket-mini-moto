<?php
namespace Database\Seeders;
use App\Models\{SalesTarget, SalesReport, Store, User};
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesTargetSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::where('is_active', true)->get();
        $employees = User::where('role', 'karyawan')->where('is_active', true)->get();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $prevMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

        $storeTargets = [
            1 => 75000000,
            2 => 55000000,
            3 => 40000000,
            4 => 30000000,
        ];

        foreach ([$prevMonth, $currentMonth] as $month) {
            $year = ($month == $prevMonth && $currentMonth == 1) ? $currentYear - 1 : $currentYear;

            foreach ($stores as $store) {
                $targetAmount = $storeTargets[$store->id] ?? 50000000;

                SalesTarget::create([
                    'store_id' => $store->id,
                    'user_id' => null,
                    'month' => $month,
                    'year' => $year,
                    'target_amount' => $targetAmount,
                ]);
            }
        }

        foreach ([$prevMonth, $currentMonth] as $month) {
            $year = ($month == $prevMonth && $currentMonth == 1) ? $currentYear - 1 : $currentYear;

            $employeeTargets = [
                1 => [1, 35000000],
                2 => [1, 30000000],
                3 => [2, 25000000],
                4 => [2, 22000000],
                5 => [3, 20000000],
                6 => [3, 18000000],
                7 => [4, 15000000],
                8 => [4, 13000000],
            ];

            foreach ($employees as $emp) {
                $empIdx = (int)filter_var($emp->name, FILTER_SANITIZE_NUMBER_INT);
                $targetAmount = $employeeTargets[$empIdx][1] ?? 20000000;

                SalesTarget::create([
                    'store_id' => null,
                    'user_id' => $emp->id,
                    'month' => $month,
                    'year' => $year,
                    'target_amount' => $targetAmount,
                ]);
            }
        }

        $this->command->info('Sales targets seeded: ' . SalesTarget::count() . ' targets created.');
    }
}
