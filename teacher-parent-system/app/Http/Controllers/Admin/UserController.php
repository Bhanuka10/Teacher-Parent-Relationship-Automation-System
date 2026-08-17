<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public function import(Request $request)
    {
        $request->validate([
            'role'     => ['required', 'in:teacher,parent'],
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $role = $request->role;

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if ($handle === false) {
            return redirect()->route('admin.users.index', ['role' => $role])
                ->with('error', 'Could not read the uploaded file.');
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return redirect()->route('admin.users.index', ['role' => $role])
                ->with('error', 'The CSV file is empty.');
        }

        $columns = array_map(fn ($col) => strtolower(trim((string) $col)), $header);

        $findColumn = function (array $aliases) use ($columns) {
            foreach ($aliases as $alias) {
                $index = array_search($alias, $columns, true);
                if ($index !== false) {
                    return $index;
                }
            }
            return null;
        };

        $nameIndex    = $findColumn(['full name', 'name']);
        $emailIndex   = $findColumn(['email address', 'email']);
        $gradeIndex   = $findColumn(['grade']);
        $sectionIndex = $findColumn(['section']);

        if ($nameIndex === null || $emailIndex === null || $gradeIndex === null || $sectionIndex === null) {
            fclose($handle);
            return redirect()->route('admin.users.index', ['role' => $role])
                ->with('error', 'The CSV must include Full name, Email address, Grade and Section columns.');
        }

        $created     = 0;
        $errors      = [];
        $seenEmails  = [];
        $rowNumber   = 1; // header row

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            $isBlankRow = count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0;
            if ($isBlankRow) {
                continue;
            }

            $name    = trim((string) ($row[$nameIndex] ?? ''));
            $email   = trim((string) ($row[$emailIndex] ?? ''));
            $grade   = trim((string) ($row[$gradeIndex] ?? ''));
            $section = strtoupper(trim((string) ($row[$sectionIndex] ?? '')));

            $validator = Validator::make(
                compact('name', 'email', 'grade', 'section'),
                [
                    'name'    => ['required', 'string', 'max:255'],
                    'email'   => ['required', 'email', 'unique:users,email'],
                    'grade'   => ['required', 'integer', 'between:1,12'],
                    'section' => ['required', 'in:A,B,C,D,E'],
                ]
            );

            if ($validator->fails()) {
                $errors[] = "Row {$rowNumber}: ".implode(' ', $validator->errors()->all());
                continue;
            }

            $emailKey = strtolower($email);
            if (isset($seenEmails[$emailKey])) {
                $errors[] = "Row {$rowNumber}: email {$email} is duplicated in this file.";
                continue;
            }
            $seenEmails[$emailKey] = true;

            $prefix   = $role === 'teacher' ? 'te-' : 'st-';
            $password = $prefix.Str::before($email, '@');

            $user = User::create([
                'name'                 => $name,
                'email'                => $email,
                'role'                 => $role,
                'password'             => Hash::make($password),
                'must_change_password' => true,
            ]);

            $className   = $grade.'-'.$section;
            $schoolClass = SchoolClass::firstOrCreate(['name' => $className]);

            if ($role === 'teacher') {
                $schoolClass->update(['teacher_id' => $user->id]);
            } else {
                Student::create([
                    'name'             => $user->name,
                    'admission_number' => 'ADM'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                    'school_class_id'  => $schoolClass->id,
                    'parent_id'        => $user->id,
                ]);
            }

            $created++;
        }

        fclose($handle);

        $roleLabel = $role === 'parent' ? 'student' : 'teacher';
        $message   = $created === 1
            ? "1 {$roleLabel} account created."
            : "{$created} {$roleLabel} accounts created.";

        if (!empty($errors)) {
            $message .= ' '.count($errors).' row(s) skipped — see details below.';
        }

        return redirect()->route('admin.users.index', ['role' => $role])
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    public function edit(User $user)
    {
        $selectedGrade = null;
        $selectedSection = null;

        $currentClass = $user->role === 'teacher'
            ? $user->schoolClass
            : $user->students()->first()?->schoolClass;

        if ($currentClass && str_contains($currentClass->name, '-')) {
            [$selectedGrade, $selectedSection] = explode('-', $currentClass->name);
        }

        return view('admin.users.edit', compact('user', 'selectedGrade', 'selectedSection'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->must_change_password = true;
        }
        $user->save();

        if (in_array($user->role, ['teacher', 'parent'], true) && !empty($validated['grade']) && !empty($validated['section'])) {
            $className = $validated['grade'].'-'.$validated['section'];
            $schoolClass = SchoolClass::firstOrCreate(['name' => $className]);

            if ($user->role === 'teacher') {
                $oldClass = $user->schoolClass;
                if ($oldClass && $oldClass->id !== $schoolClass->id) {
                    $oldClass->update(['teacher_id' => null]);
                }
                $schoolClass->update(['teacher_id' => $user->id]);
            } else {
                $student = $user->students()->first();
                if ($student) {
                    $student->update(['school_class_id' => $schoolClass->id]);
                }
            }
        }

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