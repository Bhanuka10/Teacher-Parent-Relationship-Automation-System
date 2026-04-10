<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Student;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'                 => 'Admin User',
            'email'                => 'admin@school.com',
            'password'             => Hash::make('password'),
            'role'                 => 'admin',
            'must_change_password' => false,
        ]);

        // Teacher
        $teacher = User::create([
            'name'                 => 'John Teacher',
            'email'                => 'teacher@school.com',
            'password'             => Hash::make('password'),
            'role'                 => 'teacher',
            'must_change_password' => false,
        ]);

        // Parent
        $parent = User::create([
            'name'                 => 'Mary Parent',
            'email'                => 'parent@school.com',
            'password'             => Hash::make('password'),
            'role'                 => 'parent',
            'must_change_password' => false,
        ]);

        // Class
        $class = SchoolClass::create([
            'name'        => 'Grade 10 - A',
            'description' => 'Morning batch',
            'teacher_id'  => $teacher->id,
        ]);

        // Students
        Student::create([
            'name'             => 'Alice Johnson',
            'admission_number' => 'STU-001',
            'date_of_birth'    => '2010-05-12',
            'gender'           => 'female',
            'school_class_id'  => $class->id,
            'parent_id'        => $parent->id,
        ]);

        Student::create([
            'name'             => 'Bob Smith',
            'admission_number' => 'STU-002',
            'date_of_birth'    => '2010-08-20',
            'gender'           => 'male',
            'school_class_id'  => $class->id,
            'parent_id'        => null,
        ]);
    }
}