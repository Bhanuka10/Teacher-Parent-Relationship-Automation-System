<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $fillable = ['exam_subject_id', 'student_id', 'mark', 'entered_by'];

    public function subject()
    {
        return $this->belongsTo(ExamSubject::class, 'exam_subject_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
