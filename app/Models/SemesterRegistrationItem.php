<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterRegistrationItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function registration()
    {
        return $this->belongsTo(SemesterRegistration::class, 'semester_registration_id');
    }

    public function moduleOffering()
    {
        return $this->belongsTo(ModuleOffering::class);
    }
}
