<?php

namespace App\Services;

use App\Models\SmsBatch;
use App\Models\SmsMessage;
use App\Models\SmsTemplate;
use App\Models\SmsSetting;
use App\Services\Sms\SmsProviderInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SmsService
{
    protected $provider;
    protected $phoneService;

    public function __construct(PhoneNumberService $phoneService)
    {
        $this->phoneService = $phoneService;
        $this->resolveProvider();
    }

    protected function resolveProvider()
    {
        // Safety check if table doesn't exist yet (during migration)
        try {
            $providerKey = SmsSetting::where('key', 'provider')->value('value') ?? Config::get('sms.default', 'simulated');
        } catch (\Exception $e) {
            $providerKey = Config::get('sms.default', 'simulated');
        }

        $class = match($providerKey) {
            'simulated' => \App\Services\Sms\Providers\SimulatedSmsProvider::class,
            // 'nextsms' => \App\Services\Sms\Providers\NextSmsProvider::class,
            default => \App\Services\Sms\Providers\SimulatedSmsProvider::class,
        };

        if (class_exists($class)) {
            $this->provider = new $class();
        } else {
            throw new \Exception("SMS Driver class {$class} not found.");
        }
    }

    /**
     * Send a single SMS immediately.
     */
    public function sendNow($to, $message, $recipientType = 'custom', $recipientId = null, $sentBy = null, $templateKey = null)
    {
        try {
            $enabled = SmsSetting::where('key', 'is_enabled')->value('value');
            if ($enabled === '0' || $enabled === false) {
                Log::info("SMS Sending Disabled. Skipped for $to");
                return null; 
            }
        } catch (\Exception $e) {
            // If DB fail, assume enabled or disabled? Assume enabled for simulated default
        }

        if (!($this->provider instanceof SmsProviderInterface)) {
             $this->resolveProvider();
        }

        // Create log entry
        $normalizedPhone = $this->phoneService->normalize($to);
        
        $smsLog = SmsMessage::create([
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
            'phone_number' => $normalizedPhone ?? $to,
            'message_body' => $message,
            'template_key' => $templateKey,
            'status' => 'queued',
            'sent_by' => $sentBy ?? Auth::id(),
        ]);

        if (!$normalizedPhone) {
            $smsLog->update([
                'status' => 'failed',
                'failure_reason' => 'Invalid phone number format',
            ]);
            return $smsLog;
        }

        try {
            $result = $this->provider->send($normalizedPhone, $message);

            if ($result->success) {
                $smsLog->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'meta' => array_merge($smsLog->meta ?? [], ['message_id' => $result->messageId]),
                ]);
            } else {
                $smsLog->update([
                    'status' => 'failed',
                    'failure_reason' => $result->error,
                ]);
            }

            return $smsLog;

        } catch (\Exception $e) {
            $smsLog->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
            Log::error("SMS Service Error: " . $e->getMessage());
            return $smsLog;
        }
    }

    /**
     * Parse template placeholders.
     */
    public function parseTemplate($templateKey, $data = [])
    {
        $template = SmsTemplate::where('key', $templateKey)->where('is_active', true)->first();
        if (!$template) return null;

        $message = $template->message_body;
        
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $message;
    }

    /**
     * Create a batch for later processing (or immediate bulk send).
     */
    public function createBatch($name, $filters, $messagesData, $createdBy)
    {
        $batch = SmsBatch::create([
            'name' => $name,
            'filters' => $filters,
            'total_messages' => count($messagesData),
            'status' => 'pending',
            'created_by' => $createdBy,
        ]);

        foreach ($messagesData as $data) {
            $data['sms_batch_id'] = $batch->id;
            $data['status'] = 'pending'; // Waiting for batch process
            SmsMessage::create($data);
        }

        return $batch;
    }

    /**
     * Process a batch.
     */
    public function processBatch(SmsBatch $batch)
    {
        $batch->update(['status' => 'processing', 'started_at' => now()]);

        $messages = $batch->messages()->where('status', 'pending')->get();
        $successCount = 0;
        $failCount = 0;

        foreach ($messages as $msg) {
            // Use sendNow logic but updating existing record
            // We can reuse sendNow if we pass ID, but sendNow creates new record.
            // So we copy logic or refactor.
            // For MVP, just iterate and call provider.
            
            try {
                // Check enabled
                 $enabled = SmsSetting::where('key', 'is_enabled')->value('value');
                 if ($enabled === '0') {
                     $msg->update(['status' => 'failed', 'failure_reason' => 'SMS Disabled']);
                     $failCount++;
                     continue;
                 }

                // Normalize check
                $normalized = $this->phoneService->normalize($msg->phone_number);
                if (!$normalized) {
                    $msg->update(['status' => 'failed', 'failure_reason' => 'Invalid phone']);
                    $failCount++;
                    continue;
                }

                $result = $this->provider->send($normalized, $msg->message_body);
                
                if ($result->success) {
                    $msg->update([
                        'status' => 'sent', 
                        'sent_at' => now(),
                        'meta' => array_merge($msg->meta ?? [], ['message_id' => $result->messageId])
                    ]);
                    $successCount++;
                } else {
                    $msg->update(['status' => 'failed', 'failure_reason' => $result->error]);
                    $failCount++;
                }

            } catch (\Exception $e) {
                $msg->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);
                $failCount++;
            }
        }

        $batch->update([
            'status' => 'completed',
            'completed_at' => now(),
            'success_count' => $successCount,
            'failure_count' => $failCount,
        ]);

        return $batch;
    }
}
