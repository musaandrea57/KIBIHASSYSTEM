<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructureItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'amount' => 'decimal:2',
        'amount_oct' => 'decimal:2',
        'amount_jan' => 'decimal:2',
        'amount_apr' => 'decimal:2',
    ];

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        // If amount is set directly (legacy), return it, otherwise sum installments
        if ($this->amount > 0 && ($this->amount_oct + $this->amount_jan + $this->amount_apr == 0)) {
            return $this->amount;
        }
        return $this->amount_oct + $this->amount_jan + $this->amount_apr;
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeItem()
    {
        return $this->belongsTo(FeeItem::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
