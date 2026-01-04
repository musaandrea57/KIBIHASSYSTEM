<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContinuousAssessment extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'mark' => 'decimal:2',
        'max_mark' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function moduleResult()
    {
        return $this->belongsTo(ModuleResult::class);
    }

    public function assessmentType()
    {
        return $this->belongsTo(AssessmentType::class);
    }
}
