<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterRegistration extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'semester_id',
        'program_id',
        'nta_level',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(SemesterRegistrationItem::class);
    }

    public function moduleOfferings()
    {
        return $this->belongsToMany(ModuleOffering::class, 'semester_registration_items');
    }

    public function getTotalCreditsAttribute()
    {
        return $this->items->sum('credits_snapshot');
    }
}
