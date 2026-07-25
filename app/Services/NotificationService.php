<?php
namespace App\Services;
use App\Models\Notification;
use App\Models\User;
class NotificationService {
    public static function send($userId, $type, $title, $message, $url = null) {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ]);
    }
    public static function sendToAllAdmins($type, $title, $message, $url = null) {
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            self::send($admin->id, $type, $title, $message, $url);
        }
    }
    public static function sendToStoreHeads($storeId, $type, $title, $message, $url = null) {
        $heads = User::where('role', 'kepala_toko')
            ->whereHas('stores', fn($q) => $q->where('stores.id', $storeId))
            ->where('is_active', true)->get();
        foreach ($heads as $head) {
            self::send($head->id, $type, $title, $message, $url);
        }
    }
}
