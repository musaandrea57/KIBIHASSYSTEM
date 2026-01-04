<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    protected $appends = ['balance_amount'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function feeItem()
    {
        return $this->belongsTo(FeeItem::class);
    }

    public function getBalanceAmountAttribute()
    {
        return max(0, $this->amount - $this->paid_amount);
    }
}
