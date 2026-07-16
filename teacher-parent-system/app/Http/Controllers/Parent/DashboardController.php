<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $parent = auth()->user();
        $student = $parent->students()->first();

        $todayAttendance = null;
        $latestAttendance = null;

        if ($student) {
            $todayAttendance = Attendance::where('student_id', $student->id)
                ->whereDate('date', now()->toDateString())
                ->first();

            $latestAttendance = Attendance::where('student_id', $student->id)
                ->orderByDesc('date')
                ->first();
        }

        return view('parent.dashboard', compact('parent', 'student', 'todayAttendance', 'latestAttendance'));
    }
}