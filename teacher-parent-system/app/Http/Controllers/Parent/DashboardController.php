<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\MessageRecipient;

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

        $unreadMessages = MessageRecipient::where('user_id', $parent->id)->whereNull('read_at')->count();

        return view('parent.dashboard', compact('parent', 'student', 'todayAttendance', 'latestAttendance', 'unreadMessages'));
    }
}
