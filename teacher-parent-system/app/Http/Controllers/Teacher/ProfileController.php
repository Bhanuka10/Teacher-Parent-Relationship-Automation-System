<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $teacher = auth()->user();
        $profile = $teacher->teacherProfile;
        $schoolClass = $teacher->schoolClass()->withCount('students')->first();

        return view('teacher.profile', compact('teacher', 'profile', 'schoolClass'));
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

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}
