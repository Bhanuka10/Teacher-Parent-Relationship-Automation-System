<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();
        return view('teacher.dashboard', compact('teacher'));
    }
}