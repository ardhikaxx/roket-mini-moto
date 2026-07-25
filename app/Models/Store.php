<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Store extends Model {
    protected $guarded = [];
    public function users() { return $this->belongsToMany(User::class, 'user_stores'); }
    public function salesReports() { return $this->hasMany(SalesReport::class); }
}