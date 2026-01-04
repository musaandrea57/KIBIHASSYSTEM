<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function blocks()
    {
        return $this->hasMany(HostelBlock::class);
    }

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class);
    }

    public function beds()
    {
        return $this->hasManyThrough(HostelBed::class, HostelRoom::class, 'hostel_id', 'room_id');
    }
    
    public function allocations()
    {
        return $this->hasMany(HostelAllocation::class);
    }
}
