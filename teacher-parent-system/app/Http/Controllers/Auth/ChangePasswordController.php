<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        Auth::logout();
        $request->session()->invalidate();

        return redirect()->route('login')
            ->with('success', 'Password updated. Please log in again.');
    }
}