<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->query('class_id');
        $status = $request->query('status');

        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = null;
        }

        $search = trim((string) $request->query('search', ''));

        $leaveRequests = LeaveRequest::with(['student', 'schoolClass', 'requestedBy', 'reviewedBy'])
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('admission_number', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $classes = SchoolClass::orderBy('name')->get(['id', 'name']);

        $counts = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        $searchOptions = LeaveRequest::with('student:id,name,admission_number')
            ->get()
            ->pluck('student.name')
            ->filter()
            ->unique()
            ->values();

        return view('admin.leaves.index', compact('leaveRequests', 'classes', 'classId', 'status', 'search', 'counts', 'searchOptions'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load('student', 'schoolClass', 'requestedBy', 'reviewedBy');

        return view('admin.leaves.show', compact('leaveRequest'));
    }
}
