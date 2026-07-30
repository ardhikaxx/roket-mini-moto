<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $products = DB::table('products')->whereNull('cost_price')->orWhere('cost_price', 0)->get();

        $costPrices = [
            'Mini Trail 50cc Red' => 1700000,
            'Mini Trail 50cc Blue' => 1700000,
            'Mobil Aki SUV 12V' => 1200000,
            'Mobil Aki Sedan 6V' => 750000,
            'ATV Mini Adventure' => 2200000,
            'Sepeda Listrik A30' => 3100000,
            'Sepeda Listrik C15' => 2600000,
            'Kembang Api Roman' => 90000,
            'Kembang Api Cake' => 160000,
            'Ban Dalam Ring' => 25000,
            'Karburator Racing' => 110000,
            'Helm Anak' => 160000,
            'Sarung Tangan' => 45000,
        ];

        foreach ($products as $product) {
            $cost = null;
            foreach ($costPrices as $key => $price) {
                if (str_contains($product->name, $key)) {
                    $cost = $price;
                    break;
                }
            }
            if ($cost === null) {
                $cost = (int)($product->price * 0.65);
            }
            DB::table('products')->where('id', $product->id)->update(['cost_price' => $cost]);
        }
    }

    public function down()
    {
        // Tidak perlu rollback
    }
};
