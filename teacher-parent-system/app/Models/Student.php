<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'admission_number',
        'date_of_birth',
        'gender',
        'school_class_id',
        'parent_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}