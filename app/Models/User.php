<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
    use Notifiable;
    protected $guarded = [];
    protected $hidden = ['pin', 'remember_token'];
    public function getAuthPassword() { return $this->pin; }
    public function stores() { return $this->belongsToMany(Store::class, 'user_stores'); }
    public function isAdmin() { return $this->role === 'admin'; }
    public function isKepalaToko() { return $this->role === 'kepala_toko'; }
    public function isKaryawan() { return $this->role === 'karyawan'; }
}