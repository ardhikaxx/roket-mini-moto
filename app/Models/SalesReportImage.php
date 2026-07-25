<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SalesReportImage extends Model {
    protected $guarded = [];
    public function salesReport() { return $this->belongsTo(SalesReport::class); }
}