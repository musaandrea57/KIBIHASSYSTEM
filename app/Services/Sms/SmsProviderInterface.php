<?php

namespace App\Services\Sms;

interface SmsProviderInterface
{
    /**
     * Send an SMS message.
     *
     * @param string $to Recipient phone number (E.164)
     * @param string $message Message content
     * @param array $meta Additional metadata (optional)
     * @return SmsSendResult
     */
    public function send(string $to, string $message, array $meta = []): SmsSendResult;
}
