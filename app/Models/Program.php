<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function levelRules()
    {
        return $this->hasMany(ProgrammeLevelRule::class);
    }
}
