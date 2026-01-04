<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function block()
    {
        return $this->belongsTo(HostelBlock::class);
    }

    public function beds()
    {
        return $this->hasMany(HostelBed::class, 'room_id');
    }

    public function allocations()
    {
        return $this->hasMany(HostelAllocation::class, 'room_id');
    }
    
    public function activeAllocations()
    {
        return $this->allocations()->where('status', 'active');
    }
    
    public function getOccupancyAttribute()
    {
        return $this->activeAllocations()->count();
    }
    
    public function getAvailableAttribute()
    {
        return max(0, $this->capacity - $this->occupancy);
    }
}
