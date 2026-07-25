<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $guarded = [];
    public function category() { return $this->belongsTo(Category::class); }
    public function priceHistories() { return $this->hasMany(ProductPriceHistory::class); }
    public function salesReportItems() { return $this->hasMany(SalesReportItem::class); }
}
