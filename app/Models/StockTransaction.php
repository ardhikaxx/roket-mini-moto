<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model {
    protected $fillable = [
        'product_id', 'store_id', 'user_id', 'type',
        'quantity', 'stock_before', 'stock_after',
        'reference_type', 'reference_id', 'notes'
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function store()   { return $this->belongsTo(Store::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
