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

        $leaveRequests = LeaveRequest::with(['student', 'schoolClass', 'requestedBy', 'reviewedBy'])
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $classes = SchoolClass::orderBy('name')->get(['id', 'name']);

        return view('admin.leaves.index', compact('leaveRequests', 'classes', 'classId', 'status'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load('student', 'schoolClass', 'requestedBy', 'reviewedBy');

        return view('admin.leaves.show', compact('leaveRequest'));
    }
}
