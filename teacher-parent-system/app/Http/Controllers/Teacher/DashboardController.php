<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MessageRecipient;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();
        $unreadMessages = MessageRecipient::where('user_id', $teacher->id)->whereNull('read_at')->count();

        return view('teacher.dashboard', compact('teacher', 'unreadMessages'));
    }
}
