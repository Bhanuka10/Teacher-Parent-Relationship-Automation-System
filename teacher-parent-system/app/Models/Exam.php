<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'school_class_id', 'teacher_id', 'academic_year', 'term',
        'name', 'exam_date', 'term_start_date', 'term_end_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'term_start_date' => 'date',
        'term_end_date' => 'date',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subjects()
    {
        return $this->hasMany(ExamSubject::class)->orderBy('position');
    }

    public function termLabel(): string
    {
        return "Term {$this->term} · {$this->academic_year}";
    }
}
