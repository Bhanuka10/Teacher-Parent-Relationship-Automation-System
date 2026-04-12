<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'admission_number',
        'full_name',
        'index_number',
        'date_of_birth',
        'gender',
        'parent_phone_number',
        'parent_email',
        'address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'admission_number', 'admission_number');
    }
}
