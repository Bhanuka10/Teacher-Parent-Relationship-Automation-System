<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewTeacherLeaveRequest;
use App\Models\TeacherLeaveRequest;
use Illuminate\Http\Request;

class StaffLeaveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = null;
        }

        $search = trim((string) $request->query('search', ''));

        $leaveRequests = TeacherLeaveRequest::with('teacher')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('teacher', function ($teacherQuery) use ($search) {
                    $teacherQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->get();

        $counts = [
            'total' => TeacherLeaveRequest::count(),
            'pending' => TeacherLeaveRequest::where('status', 'pending')->count(),
            'approved' => TeacherLeaveRequest::where('status', 'approved')->count(),
            'rejected' => TeacherLeaveRequest::where('status', 'rejected')->count(),
        ];

        $searchOptions = TeacherLeaveRequest::with('teacher:id,name')
            ->get()
            ->pluck('teacher.name')
            ->filter()
            ->unique()
            ->values();

        return view('admin.staff-leaves.index', compact('leaveRequests', 'status', 'search', 'counts', 'searchOptions'));
    }

    public function show(TeacherLeaveRequest $teacherLeaveRequest)
    {
        $teacherLeaveRequest->load('teacher', 'reviewedBy');

        return view('admin.staff-leaves.show', compact('teacherLeaveRequest'));
    }

    public function review(ReviewTeacherLeaveRequest $request, TeacherLeaveRequest $teacherLeaveRequest)
    {
        abort_unless($teacherLeaveRequest->isPending(), 422, 'This request has already been reviewed.');

        $validated = $request->validated();

        $teacherLeaveRequest->update([
            'status' => $validated['decision'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        return redirect()->route('admin.staff-leaves.show', $teacherLeaveRequest)->with('success', 'Leave request '.$validated['decision'].'.');
    }
}
