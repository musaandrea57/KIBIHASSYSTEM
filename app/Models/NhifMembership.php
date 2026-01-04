<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $student_id
 * @property string $card_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $issued_date
 * @property \Illuminate\Support\Carbon|null $expiry_date
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $last_checked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $status_badge
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class NhifMembership extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'last_checked_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function verificationLogs()
    {
        return $this->hasMany(NhifVerificationLog::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'pending_verification' => 'warning',
            default => 'secondary',
        };
    }
}
