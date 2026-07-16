<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'address',
        'phone_number',
        'email_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
