<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $teacher = auth()->user();
        $profile = $teacher->teacherProfile;

        return view('teacher.profile', compact('teacher', 'profile'));
    }

    public function update(Request $request)
    {
        $teacher = auth()->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'email_address' => ['required', 'email', 'max:255'],
        ]);

        TeacherProfile::updateOrCreate(
            ['user_id' => $teacher->id],
            $validated
        );

        return back()->with('success', 'Teacher profile updated successfully.');
    }
}
