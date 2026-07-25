<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SalesReport extends Model {
    protected $guarded = [];
    public function user() { return $this->belongsTo(User::class); }
    public function store() { return $this->belongsTo(Store::class); }
    public function items() { return $this->hasMany(SalesReportItem::class); }
    public function images() { return $this->hasMany(SalesReportImage::class); }
    public function statusHistories() { return $this->hasMany(ReportStatusHistory::class); }
}
