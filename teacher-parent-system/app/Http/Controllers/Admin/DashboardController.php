<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /* ── Summary stats ── */
        $stats = [
            'teachers' => User::where('role', 'teacher')->count(),
            'parents'  => User::where('role', 'parent')->count(),
            'students' => Student::count(),
            'classes'  => SchoolClass::count(),
        ];

        /* ── Last 7 days attendance trend (present / absent per day) ── */
        $last7Days = collect(range(6, 0))->map(fn($d) => Carbon::today()->subDays($d)->toDateString());

        $attendanceByDay = Attendance::select('date', 'status', DB::raw('count(*) as total'))
            ->whereBetween('date', [$last7Days->first(), $last7Days->last()])
            ->groupBy('date', 'status')
            ->get()
            ->groupBy('date');

        $trendLabels  = [];
        $trendPresent = [];
        $trendAbsent  = [];

        foreach ($last7Days as $day) {
            $trendLabels[]  = Carbon::parse($day)->format('D d');
            $dayData        = $attendanceByDay->get($day, collect());
            $trendPresent[] = (int) ($dayData->firstWhere('status', 'present')?->total ?? 0);
            $trendAbsent[]  = (int) ($dayData->firstWhere('status', 'absent')?->total  ?? 0);
        }

        /* ── Students per class (top 10) ── */
        $classDistribution = SchoolClass::withCount('students')
            ->orderByDesc('students_count')
            ->limit(10)
            ->get();

        $classLabels   = $classDistribution->pluck('name')->toArray();
        $classStudents = $classDistribution->pluck('students_count')->toArray();

        /* ── Attendance rate per class (all-time present %) ── */
        $classRates = SchoolClass::with('students')->get()->map(function ($class) {
            $studentIds = $class->students->pluck('id');
            if ($studentIds->isEmpty()) return null;
            $total   = Attendance::whereIn('student_id', $studentIds)->count();
            $present = Attendance::whereIn('student_id', $studentIds)->where('status', 'present')->count();
            return [
                'name' => $class->name,
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        })->filter()->sortByDesc('rate')->take(8)->values();

        $rateLabels = $classRates->pluck('name')->toArray();
        $rateValues = $classRates->pluck('rate')->toArray();

        /* ── Today's quick stats ── */
        $today        = Carbon::today()->toDateString();
        $todayPresent = Attendance::whereDate('date', $today)->where('status', 'present')->count();
        $todayAbsent  = Attendance::whereDate('date', $today)->where('status', 'absent')->count();
        $todayTotal   = $todayPresent + $todayAbsent;

        /* ── Recent attendance records (last 8) ── */
        $recentRecords = Attendance::with(['student.schoolClass', 'markedBy'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'trendLabels', 'trendPresent', 'trendAbsent',
            'classLabels', 'classStudents',
            'rateLabels', 'rateValues',
            'todayPresent', 'todayAbsent', 'todayTotal',
            'recentRecords'
        ));
    }
}
