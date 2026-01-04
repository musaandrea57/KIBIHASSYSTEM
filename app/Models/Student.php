<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $registration_number
 * @property string|null $nactvet_registration_number
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string $gender
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $phone
 * @property int $program_id
 * @property int $current_nta_level
 * @property int|null $current_academic_year_id
 * @property int|null $current_semester_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $balance
 * @property-read User $user
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Student extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moduleResults()
    {
        return $this->hasMany(ModuleResult::class);
    }
    
    public function results()
    {
        // Legacy support or alias
        return $this->moduleResults();
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function currentAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'current_academic_year_id');
    }

    public function currentSemester()
    {
        return $this->belongsTo(Semester::class, 'current_semester_id');
    }

    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    public function semesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration::class);
    }

    public function guardians()
    {
        return $this->belongsToMany(User::class, 'student_guardians', 'student_id', 'user_id')
                    ->withPivot('relationship')
                    ->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute()
    {
        $totalInvoiced = $this->invoices()->sum('subtotal'); // Adjusted to match schema (subtotal/balance)
        // Or use balance column from invoices?
        // Better to use Invoice balance sum.
        return $this->invoices()->sum('balance');
    }

    public function isFeeCleared()
    {
        // Use service logic or simple check
        // For now simple check on balance <= 0
        return $this->balance <= 0.01;
    }
}
