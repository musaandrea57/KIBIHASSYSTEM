<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelBlock extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class, 'block_id');
    }
}
