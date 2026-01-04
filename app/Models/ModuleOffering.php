<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleOffering extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assignments()
    {
        return $this->hasMany(ModuleAssignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(ModuleAssignment::class)->where('status', 'active');
    }

    public function moduleResults()
    {
        return $this->hasMany(ModuleResult::class);
    }
}
