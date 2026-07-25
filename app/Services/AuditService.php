<?php
namespace App\Services;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
class AuditService {
    public static function log($action, $description, $model = null, $modelId = null, $changes = null) {
        if (!Auth::check()) return;
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'model' => $model,
            'model_id' => $modelId,
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}
