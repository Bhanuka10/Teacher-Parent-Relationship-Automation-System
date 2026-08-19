<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\CreateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function create()
    {
        $schoolClass = auth()->user()->schoolClass;

        if (!$schoolClass) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'You are not assigned to a class yet, so you cannot add students. Contact an admin.');
        }

        return view('teacher.students.create', compact('schoolClass'));
    }

    public function store(CreateStudentRequest $request)
    {
        $schoolClass = auth()->user()->schoolClass;
        abort_unless($schoolClass, 403);

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'parent',
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
        ]);

        Student::create([
            'name' => $user->name,
            'admission_number' => 'ADM'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
            'school_class_id' => $schoolClass->id,
            'parent_id' => $user->id,
        ]);

        return redirect()->route('teacher.students.create')->with('success', 'Student account created.');
    }

    public function import(Request $request)
    {
        $schoolClass = auth()->user()->schoolClass;
        abort_unless($schoolClass, 403);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if ($handle === false) {
            return redirect()->route('teacher.students.create')->with('error', 'Could not read the uploaded file.');
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return redirect()->route('teacher.students.create')->with('error', 'The CSV file is empty.');
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

        $nameIndex = $findColumn(['full name', 'name']);
        $emailIndex = $findColumn(['email address', 'email']);

        if ($nameIndex === null || $emailIndex === null) {
            fclose($handle);
            return redirect()->route('teacher.students.create')->with('error', 'The CSV must include Full name and Email address columns.');
        }

        $created = 0;
        $errors = [];
        $seenEmails = [];
        $rowNumber = 1; // header row

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            $isBlankRow = count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0;
            if ($isBlankRow) {
                continue;
            }

            $name = trim((string) ($row[$nameIndex] ?? ''));
            $email = trim((string) ($row[$emailIndex] ?? ''));

            $validator = Validator::make(
                compact('name', 'email'),
                [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'unique:users,email'],
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

            $password = 'st-'.Str::before($email, '@');

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'role' => 'parent',
                'password' => Hash::make($password),
                'must_change_password' => true,
            ]);

            Student::create([
                'name' => $user->name,
                'admission_number' => 'ADM'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                'school_class_id' => $schoolClass->id,
                'parent_id' => $user->id,
            ]);

            $created++;
        }

        fclose($handle);

        $message = $created === 1
            ? '1 student account created.'
            : "{$created} student accounts created.";

        if (!empty($errors)) {
            $message .= ' '.count($errors).' row(s) skipped — see details below.';
        }

        return redirect()->route('teacher.students.create')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
