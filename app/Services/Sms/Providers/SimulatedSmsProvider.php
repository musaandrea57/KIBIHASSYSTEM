<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\SmsProviderInterface;
use App\Services\Sms\SmsSendResult;
use Illuminate\Support\Facades\Log;

class SimulatedSmsProvider implements SmsProviderInterface
{
    public function send(string $to, string $message, array $meta = []): SmsSendResult
    {
        // Simulate logging
        Log::channel('single')->info("SMS SIMULATION TO [{$to}]: {$message}", $meta);

        // Simple validation simulation
        // Allow +digits, digits, spaces, dashes
        $cleanNumber = preg_replace('/[^0-9+]/', '', $to);

        if (strlen($cleanNumber) < 9) {
             return new SmsSendResult(
                 false,
                 null,
                 'Invalid phone number length',
                 ['to' => $to]
             );
        }

        return new SmsSendResult(
            true,
            'SIM-' . uniqid() . '-' . time(),
            null,
            ['to' => $to, 'simulated' => true]
        );
    }
}
