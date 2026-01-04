<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'voided_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    /**
     * Recalculate totals based on items and payments
     */
    public function recalculateTotals()
    {
        if ($this->status === 'voided') {
            return;
        }

        // 1. Calculate Subtotal from Items
        $this->subtotal = $this->items->sum('amount');

        // 2. Calculate Total Paid from NON-REVERSED Payments
        // Note: strictly speaking, we should sum the allocations to this invoice's items + any general payments linked to this invoice
        // However, for simplicity and robustness, we can sum the 'paid_amount' on invoice items, 
        // which should be updated whenever a payment is allocated.
        // Let's rely on InvoiceItem's paid_amount which we must keep in sync.
        
        $this->total_paid = $this->items->sum('paid_amount');

        // 3. Calculate Balance
        $this->balance = round($this->subtotal - $this->total_paid, 2);

        // 4. Update Status
        if ($this->balance <= 0 && $this->subtotal > 0) {
            $this->status = 'paid';
            $this->balance = 0; // Prevent negative balance if overpaid (though allocations shouldn't allow)
        } elseif ($this->total_paid > 0 && $this->total_paid < $this->subtotal) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
    }
}
