<?php

namespace App\Services;

use App\Models\FinanceAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class FinanceAuditService
{
    /**
     * Log a finance action.
     *
     * @param string $action The action performed (e.g., 'create_invoice', 'void_invoice')
     * @param string $modelType The model class name (e.g., Invoice::class)
     * @param int $modelId The ID of the model
     * @param array|null $payload Optional data related to the action
     * @param string|null $reason Optional reason for the action
     * @return FinanceAuditLog
     */
    public static function log(string $action, string $modelType, int $modelId, ?array $payload = null, ?string $description = null)
    {
        return FinanceAuditLog::create([
            'user_id' => Auth::id() ?? 1, // Fallback for seeding/testing
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'payload' => $payload,
            'description' => $description,
        ]);
    }
}
