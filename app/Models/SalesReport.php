<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model {
    protected $fillable = [
        'user_id', 'store_id', 'total_amount', 'total_items',
        'transaction_date', 'status', 'notes', 'rejection_reason',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function store() { return $this->belongsTo(Store::class); }
    public function items() { return $this->hasMany(SalesReportItem::class); }
    public function images() { return $this->hasMany(SalesReportImage::class); }
    public function statusHistories() { return $this->hasMany(ReportStatusHistory::class); }
}
