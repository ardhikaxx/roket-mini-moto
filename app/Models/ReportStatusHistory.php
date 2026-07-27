<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReportStatusHistory extends Model {
    protected $fillable = [
        'sales_report_id', 'user_id', 'from_status', 'to_status', 'notes',
    ];

    protected $table = 'report_status_histories';

    public function salesReport() { return $this->belongsTo(SalesReport::class); }
    public function user() { return $this->belongsTo(User::class); }
}
