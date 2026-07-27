<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Store extends Model {
    protected $fillable = [
        'name', 'address', 'phone', 'email',
        'description', 'photo', 'is_active',
    ];

    public function users() { return $this->belongsToMany(User::class, 'user_stores'); }
    public function salesReports() { return $this->hasMany(SalesReport::class); }
    public function getKepalaTokoAttribute() {
        return $this->users()->where('role', 'kepala_toko')->first();
    }
    public function getKaryawanCountAttribute() {
        return $this->users()->where('role', 'karyawan')->count();
    }
}
