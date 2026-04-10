<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isTeacher(): bool { return $this->role === 'teacher'; }
    public function isParent(): bool  { return $this->role === 'parent'; }

    public function schoolClass()
    {
        return $this->hasOne(SchoolClass::class, 'teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}