<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');

        $usersQuery = User::whereIn('role', ['teacher', 'parent']);

        if (in_array($role, ['teacher', 'parent'], true)) {
            $usersQuery->where('role', $role);
        } else {
            $role = null;
        }

        $users = $usersQuery->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'role'));
    }

    public function create()
    {
        $role = request()->query('role');
        if (!in_array($role, ['teacher', 'parent'], true)) {
            $role = null;
        }

        return view('admin.users.create', compact('role'));
    }

    public function store(CreateUserRequest $request)
    {
        $role = $request->role;

        User::create([
            'name'                 => $request->name,
            'email'                => $request->email,
            'role'                 => $role,
            'password'             => Hash::make($request->password),
            'must_change_password' => true,
        ]);

        $roleLabel = $role === 'parent' ? 'Student' : ucfirst($role);

        return redirect()->route('admin.users.index', ['role' => $role])
            ->with('success', $roleLabel.' account created.');
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

        return redirect()->route('admin.users.index', ['role' => $user->role])
            ->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $role = $user->role;
        $user->delete();
        return redirect()->route('admin.users.index', ['role' => $role])->with('success', 'User deleted.');
    }
}