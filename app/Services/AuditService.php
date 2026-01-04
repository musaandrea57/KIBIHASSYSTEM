<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log(string $action, ?Model $model = null, array $old = [], array $new = [], ?string $notes = null)
    {
        $userId = Auth::id(); // Can be null for system actions or guests
        
        // If we have models, we can auto-detect differences if not provided
        if ($model && empty($old) && empty($new)) {
             // Logic to diff model changes could go here if we were using observers
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'old_values' => !empty($old) ? $old : null,
            'new_values' => !empty($new) ? $new : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
