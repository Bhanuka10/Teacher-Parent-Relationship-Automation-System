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
        User::updateOrCreate([
            'email' => 'admin@school.com',
        ], [
            'name'                 => 'Admin User',
            'password'             => Hash::make('password'),
            'role'                 => 'admin',
            'must_change_password' => false,
        ]);

        // Teacher
        $teacher = User::updateOrCreate([
            'email' => 'teacher@school.com',
        ], [
            'name'                 => 'John Teacher',
            'password'             => Hash::make('password'),
            'role'                 => 'teacher',
            'must_change_password' => false,
        ]);

        // Parent
        $parent = User::updateOrCreate([
            'email' => 'parent@school.com',
        ], [
            'name'                 => 'Mary Parent',
            'password'             => Hash::make('password'),
            'role'                 => 'parent',
            'must_change_password' => false,
        ]);

        // Class
        $class = SchoolClass::updateOrCreate([
            'name' => 'Grade 10 - A',
        ], [
            'name'        => 'Grade 10 - A',
            'description' => 'Morning batch',
            'teacher_id'  => $teacher->id,
        ]);

        // Students
        Student::updateOrCreate([
            'admission_number' => 'STU-001',
        ], [
            'name'             => 'Alice Johnson',
            'date_of_birth'    => '2010-05-12',
            'gender'           => 'female',
            'school_class_id'  => $class->id,
            'parent_id'        => $parent->id,
        ]);

        Student::updateOrCreate([
            'admission_number' => 'STU-002',
        ], [
            'name'             => 'Bob Smith',
            'date_of_birth'    => '2010-08-20',
            'gender'           => 'male',
            'school_class_id'  => $class->id,
            'parent_id'        => null,
        ]);
    }
}