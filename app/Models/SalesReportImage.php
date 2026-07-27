<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesReportImage extends Model {
    protected $fillable = ['sales_report_id', 'image_path'];

    public function salesReport() { return $this->belongsTo(SalesReport::class); }
}