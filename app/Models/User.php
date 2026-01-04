<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function teacherAssignments()
    {
        return $this->hasMany(ModuleAssignment::class, 'teacher_user_id');
    }

    public function activeTeacherAssignments()
    {
        return $this->teacherAssignments()->where('status', 'active');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function guardianRecords()
    {
        return $this->hasMany(StudentGuardian::class);
    }

    public function children()
    {
        return $this->belongsToMany(Student::class, 'student_guardians', 'user_id', 'student_id')
                    ->withPivot('relationship')
                    ->withTimestamps();
    }

    // Messaging Relationships
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->belongsToMany(Message::class, 'message_recipients', 'recipient_id', 'message_id')
                    ->withPivot(['read_at', 'acknowledged_at', 'is_archived'])
                    ->withTimestamps();
    }

    public function messageRecipients()
    {
        return $this->hasMany(MessageRecipient::class, 'recipient_id');
    }

    public function unreadMessagesCount()
    {
        return $this->messageRecipients()->whereNull('read_at')->count();
    }
}
