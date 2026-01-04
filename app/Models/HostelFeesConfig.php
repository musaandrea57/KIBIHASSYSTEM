<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelFeesConfig extends Model
{
    use HasFactory;

    protected $table = 'hostel_fees_config';
    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function feeItem()
    {
        return $this->belongsTo(FeeItem::class);
    }
}
