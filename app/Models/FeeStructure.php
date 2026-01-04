<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
    ];

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
        return $this->hasMany(FeeStructureItem::class)->orderBy('sort_order');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Calculate total amount (using items' computed total)
    public function getTotalAmountAttribute()
    {
        return $this->items->sum('total_amount');
    }

    public function getTotalTuitionAttribute()
    {
        return $this->items->filter(function ($item) {
            return $item->feeItem && ($item->feeItem->category === 'tuition' || str_contains(strtolower($item->feeItem->name), 'tuition'));
        })->sum('total_amount');
    }

    public function getTotalOtherAttribute()
    {
        return $this->items->filter(function ($item) {
            return !$item->feeItem || ($item->feeItem->category !== 'tuition' && !str_contains(strtolower($item->feeItem->name), 'tuition'));
        })->sum('total_amount');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['active', 'archived']); // Assuming published includes active and archived? Or just published flag?
        // Prompt says: "Only one 'active published' fee structure".
        // Let's stick to status 'active' as the main "Published and Current" state.
    }
}
