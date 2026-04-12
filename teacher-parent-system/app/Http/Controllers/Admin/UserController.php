<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $search = trim((string) $request->query('search', ''));

        $usersQuery = User::whereIn('role', ['teacher', 'parent']);

        if (in_array($role, ['teacher', 'parent'], true)) {
            $usersQuery->where('role', $role);
        } else {
            $role = null;
        }

        if ($role === 'teacher') {
            $usersQuery->with(['schoolClass', 'teacherProfile']);
        } elseif ($role === 'parent') {
            $usersQuery->with(['students' => function ($query) {
                $query->with(['schoolClass', 'profile'])->orderBy('id');
            }]);
        } else {
            $usersQuery->with(['schoolClass', 'teacherProfile', 'students.schoolClass', 'students.profile']);
        }

        if ($search !== '') {
            $usersQuery->where(function ($query) use ($search, $role) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');

                if ($role === 'teacher') {
                    $query->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', '%'.$search.'%');
                    })->orWhereHas('teacherProfile', function ($profileQuery) use ($search) {
                        $profileQuery->where('full_name', 'like', '%'.$search.'%')
                            ->orWhere('phone_number', 'like', '%'.$search.'%')
                            ->orWhere('email_address', 'like', '%'.$search.'%')
                            ->orWhere('address', 'like', '%'.$search.'%');
                    });
                } elseif ($role === 'parent') {
                    $query->orWhereHas('students', function ($studentQuery) use ($search) {
                        $studentQuery->where('admission_number', 'like', '%'.$search.'%')
                            ->orWhere('name', 'like', '%'.$search.'%')
                            ->orWhereHas('profile', function ($profileQuery) use ($search) {
                                $profileQuery->where('index_number', 'like', '%'.$search.'%')
                                    ->orWhere('full_name', 'like', '%'.$search.'%');
                            })
                            ->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                                $classQuery->where('name', 'like', '%'.$search.'%');
                            });
                    });
                } else {
                    $query->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', '%'.$search.'%');
                    })->orWhereHas('students', function ($studentQuery) use ($search) {
                        $studentQuery->where('admission_number', 'like', '%'.$search.'%')
                            ->orWhere('name', 'like', '%'.$search.'%')
                            ->orWhereHas('profile', function ($profileQuery) use ($search) {
                                $profileQuery->where('index_number', 'like', '%'.$search.'%')
                                    ->orWhere('full_name', 'like', '%'.$search.'%');
                            })
                            ->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                                $classQuery->where('name', 'like', '%'.$search.'%');
                            });
                    });
                }
            });
        }

        $users = $usersQuery->orderBy('name')->get();

        $searchOptions = collect();
        if ($role === 'teacher') {
            $searchOptions = SchoolClass::whereNotNull('teacher_id')
                ->orderBy('name')
                ->pluck('name');
        } elseif ($role === 'parent') {
            $classNames = Student::whereNotNull('parent_id')
                ->with('schoolClass')
                ->get()
                ->pluck('schoolClass.name')
                ->filter();

            $admissions = Student::whereNotNull('parent_id')
                ->orderBy('admission_number')
                ->pluck('admission_number');

            $searchOptions = $classNames->merge($admissions);
        }

        $searchOptions = $searchOptions->filter()->unique()->values();

        return view('admin.users.index', compact('users', 'role', 'search', 'searchOptions'));
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

        $user = User::create([
            'name'                 => $request->name,
            'email'                => $request->email,
            'role'                 => $role,
            'password'             => Hash::make($request->password),
            'must_change_password' => true,
        ]);

        if ($role === 'teacher') {
            $className = $request->grade.'-'.$request->section;
            $schoolClass = SchoolClass::firstOrCreate(['name' => $className]);
            $schoolClass->update(['teacher_id' => $user->id]);
        } elseif ($role === 'parent') {
            $className = $request->grade.'-'.$request->section;
            $schoolClass = SchoolClass::firstOrCreate(['name' => $className]);

            Student::create([
                'name' => $user->name,
                'admission_number' => 'ADM'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                'school_class_id' => $schoolClass->id,
                'parent_id' => $user->id,
            ]);
        }

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