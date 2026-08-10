<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    // "Homework" is grammatically uncountable, so Eloquent's default
    // pluralization would guess the table name as `homework` (singular),
    // which collides with the FK column names elsewhere. Table is `homeworks`.
    protected $table = 'homeworks';

    protected $fillable = [
        'school_class_id', 'teacher_id', 'type', 'title', 'instructions',
        'allowed_file_types', 'max_marks', 'due_at', 'duration_minutes',
    ];

    protected $casts = [
        'allowed_file_types' => 'array',
        'due_at' => 'datetime',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->hasMany(HomeworkQuestion::class)->orderBy('position');
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    public function isPastDue(): bool
    {
        return $this->due_at !== null && $this->due_at->isPast();
    }
}
