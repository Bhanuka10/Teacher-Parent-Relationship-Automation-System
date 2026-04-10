<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers' => User::where('role', 'teacher')->count(),
            'parents'  => User::where('role', 'parent')->count(),
            'students' => Student::count(),
            'classes'  => SchoolClass::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}