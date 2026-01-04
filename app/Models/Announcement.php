<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_archived' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'audience' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute()
    {
        if ($this->status === 'archived') {
            return 'Archived';
        }
        if ($this->status === 'draft') {
            return 'Draft';
        }
        if ($this->start_date && $this->start_date > now()) {
            return 'Scheduled';
        }
        if ($this->end_date && $this->end_date < now()) {
            return 'Expired';
        }
        return 'Active';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status_label) {
            'Active' => 'green',
            'Scheduled' => 'blue',
            'Draft' => 'gray',
            'Archived' => 'yellow',
            'Expired' => 'red',
            default => 'gray',
        };
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published')
                     ->where(function($q) {
                         $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                     })
                     ->where(function($q) {
                         $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                     });
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
}
