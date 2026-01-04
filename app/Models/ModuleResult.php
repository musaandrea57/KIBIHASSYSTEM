<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $student_id
 * @property int $module_offering_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property float|null $cw_mark
 * @property float|null $se_mark
 * @property float|null $total_mark
 * @property string|null $grade
 * @property float|null $grade_point
 * @property float|null $points
 * @property string|null $remark
 * @property float|null $credits_snapshot
 * @property float|null $gpa_contribution
 * @property string $status
 * @property int|null $uploaded_by
 * @property int|null $published_by
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ModuleResult extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'credits_snapshot' => 'decimal:1',
        'cw_mark' => 'decimal:2',
        'se_mark' => 'decimal:2',
        'total_mark' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'points' => 'decimal:2',
        'gpa_contribution' => 'decimal:3',
        'published_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function moduleOffering()
    {
        return $this->belongsTo(ModuleOffering::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function continuousAssessments()
    {
        return $this->hasMany(ContinuousAssessment::class);
    }
}
