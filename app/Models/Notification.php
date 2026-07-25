<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Notification extends Model {
    protected $guarded = [];
    protected $casts = ['is_read' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function scopeUnread($q) { return $q->where('is_read', false); }
}
