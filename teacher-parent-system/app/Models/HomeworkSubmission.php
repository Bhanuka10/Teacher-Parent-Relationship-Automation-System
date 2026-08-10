<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkSubmission extends Model
{
    protected $fillable = [
        'homework_id', 'student_id', 'submitted_by',
        'file_path', 'file_original_name',
        'started_at', 'expires_at', 'auto_score',
        'submitted_at', 'marks', 'feedback', 'graded_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function answers()
    {
        return $this->hasMany(HomeworkAnswer::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isGraded(): bool
    {
        return $this->graded_at !== null;
    }

    public function isExpired(): bool
    {
        if ($this->isSubmitted()) {
            return false;
        }

        // Quiz attempts have their own expires_at once started; anything else
        // (not-yet-started quizzes, and all file homework) falls back to the
        // parent homework's overall due date.
        if ($this->expires_at !== null) {
            return $this->expires_at->isPast();
        }

        return $this->homework?->due_at !== null && $this->homework->due_at->isPast();
    }

    public function status(): string
    {
        if ($this->isGraded()) {
            return 'graded';
        }

        if ($this->isSubmitted()) {
            return 'submitted';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        return 'pending';
    }
}
