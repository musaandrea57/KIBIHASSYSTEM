<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhifVerificationLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'checked_at' => 'datetime',
        'response_payload' => 'array',
    ];

    public function membership()
    {
        return $this->belongsTo(NhifMembership::class, 'nhif_membership_id');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
