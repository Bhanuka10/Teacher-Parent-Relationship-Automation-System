<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingSchemeBand extends Model
{
    protected $fillable = ['min_mark', 'max_mark', 'grade', 'is_passing', 'position'];

    protected $casts = [
        'is_passing' => 'boolean',
    ];
}
