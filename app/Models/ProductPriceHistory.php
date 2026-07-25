<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductPriceHistory extends Model {
    protected $guarded = [];
    protected $table = 'product_price_histories';
    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
}
