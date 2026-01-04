<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelBed extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(HostelRoom::class);
    }

    public function allocations()
    {
        return $this->hasMany(HostelAllocation::class, 'bed_id');
    }
    
    public function activeAllocation()
    {
        return $this->hasOne(HostelAllocation::class, 'bed_id')->where('status', 'active');
    }
}
