<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        $grade = $request->query('grade');
        $section = $request->query('section');
        $date = $request->query('date');

        $query = Attendance::with([
                'student.profile',
                'student.schoolClass',
                'markedBy',
            ])
            ->when($date, function ($q) use ($date) {
                $q->whereDate('date', $date);
            })
            ->when($grade || $section, function ($q) use ($grade, $section) {
                $q->whereHas('student.schoolClass', function ($classQuery) use ($grade, $section) {
                    if ($grade && $section) {
                        $classQuery->where('name', $grade.'-'.$section);
                        return;
                    }
                    if ($grade) {
                        $classQuery->where('name', 'like', $grade.'-%');
                    }
                    if ($section) {
                        $classQuery->where('name', 'like', '%-'.$section);
                    }
                });
            })
            ->orderByDesc('date')
            ->orderBy('student_id');

        $total   = $query->count();
        $records = $query->paginate(10)->withQueryString();

        return view('admin.attendance.history', compact('records', 'grade', 'section', 'date', 'total'));
    }
}
