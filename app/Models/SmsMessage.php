<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $sms_batch_id
 * @property string $recipient_type
 * @property int $recipient_id
 * @property string $phone_number
 * @property string $message
 * @property string $status
 * @property string|null $gateway_id
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class SmsMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
        'meta' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(SmsBatch::class, 'sms_batch_id');
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
