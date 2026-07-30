<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model {
    protected $fillable = ['store_id', 'user_id', 'month', 'year', 'target_amount'];

    public function store() { return $this->belongsTo(Store::class); }
    public function user()  { return $this->belongsTo(User::class); }

    public function getMonthNameAttribute() {
        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return $months[$this->month - 1] ?? $this->month;
    }

    public function getAchievedAttribute() {
        $query = SalesReport::where('status', 'disetujui');
        if ($this->store_id) $query->where('store_id', $this->store_id);
        if ($this->user_id)  $query->where('user_id', $this->user_id);
        return $query->whereYear('transaction_date', $this->year)
                     ->whereMonth('transaction_date', $this->month)
                     ->sum('total_amount') ?? 0;
    }

    public function getPercentageAttribute() {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->achieved / $this->target_amount) * 100, 1));
    }
}
