<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkQuestion extends Model
{
    protected $fillable = ['homework_id', 'type', 'question', 'marks', 'position'];

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function options()
    {
        return $this->hasMany(HomeworkQuestionOption::class)->orderBy('position');
    }

    public function isMcq(): bool
    {
        return $this->type === 'mcq';
    }
}
