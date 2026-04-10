<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['teacher', 'parent'])
            ->orderBy('role')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(CreateUserRequest $request)
    {
        User::create([
            'name'                 => $request->name,
            'email'                => $request->email,
            'role'                 => $request->role,
            'password'             => Hash::make($request->password),
            'must_change_password' => true,
        ]);
        return redirect()->route('admin.users.index')
            ->with('success', ucfirst($request->role).' account created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->must_change_password = true;
        }
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}