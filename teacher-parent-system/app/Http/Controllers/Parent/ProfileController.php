<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $student = auth()->user()->students()->with('profile')->first();
        $profile = $student?->profile;

        return view('parent.profile', compact('student', 'profile'));
    }

    public function updateStudentProfile(Request $request)
    {
        $student = auth()->user()->students()->first();

        if (!$student) {
            return back()->withErrors(['profile' => 'Student record not found for this account.']);
        }

        $currentProfile = $student->profile;

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'index_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('student_profiles', 'index_number')->ignore($currentProfile?->id),
            ],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'parent_phone_number' => ['nullable', 'string', 'max:30'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        StudentProfile::updateOrCreate(
            ['admission_number' => $student->admission_number],
            $validated
        );

        return back()->with('success', 'Student profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:6', 'confirmed'],
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