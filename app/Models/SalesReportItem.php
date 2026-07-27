<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesReportItem extends Model {
    protected $fillable = [
        'sales_report_id', 'product_id', 'product_name',
        'quantity', 'price', 'subtotal',
    ];

    public function salesReport() { return $this->belongsTo(SalesReport::class); }
    public function product() { return $this->belongsTo(Product::class); }
}