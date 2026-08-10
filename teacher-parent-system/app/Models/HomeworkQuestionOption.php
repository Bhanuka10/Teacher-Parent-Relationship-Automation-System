<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkQuestionOption extends Model
{
    protected $fillable = ['homework_question_id', 'option_text', 'is_correct', 'position'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(HomeworkQuestion::class, 'homework_question_id');
    }
}
