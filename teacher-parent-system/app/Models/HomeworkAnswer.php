<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkAnswer extends Model
{
    protected $fillable = [
        'homework_submission_id', 'homework_question_id', 'selected_option_id',
        'answer_text', 'is_correct', 'marks_awarded',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function submission()
    {
        return $this->belongsTo(HomeworkSubmission::class, 'homework_submission_id');
    }

    public function question()
    {
        return $this->belongsTo(HomeworkQuestion::class, 'homework_question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(HomeworkQuestionOption::class, 'selected_option_id');
    }
}
