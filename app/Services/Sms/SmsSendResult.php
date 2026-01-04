<?php

namespace App\Services\Sms;

class SmsSendResult
{
    public bool $success;
    public ?string $messageId;
    public ?string $error;
    public array $raw;

    public function __construct(bool $success, ?string $messageId = null, ?string $error = null, array $raw = [])
    {
        $this->success = $success;
        $this->messageId = $messageId;
        $this->error = $error;
        $this->raw = $raw;
    }
}
