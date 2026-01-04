<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'biodata' => 'array',
        'education_background' => 'array',
        'documents' => 'array', // Legacy JSON column, might migrate away from this to 'documents' table relationship
        'emergency_contact' => 'array',
        'dob' => 'date',
        'submitted_at' => 'datetime',
        'has_disability' => 'boolean',
        'declaration_accepted' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // New relationship for the separate documents table
    public function uploadedDocuments()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
