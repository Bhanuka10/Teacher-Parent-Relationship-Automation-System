<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubject extends Model
{
    protected $fillable = ['exam_id', 'name', 'max_mark', 'position'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }
}
