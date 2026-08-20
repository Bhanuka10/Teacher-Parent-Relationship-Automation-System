<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ReviewLeaveRequest;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass;

        $emptyCounts = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0];

        if (!$schoolClass) {
            return view('teacher.leaves.index', ['schoolClass' => null, 'leaveRequests' => collect(), 'status' => null, 'counts' => $emptyCounts]);
        }

        $status = $request->query('status');
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = null;
        }

        $leaveRequests = LeaveRequest::where('school_class_id', $schoolClass->id)
            ->with('student')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->get();

        $statusCounts = LeaveRequest::where('school_class_id', $schoolClass->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'pending'  => (int) ($statusCounts['pending'] ?? 0),
            'approved' => (int) ($statusCounts['approved'] ?? 0),
            'rejected' => (int) ($statusCounts['rejected'] ?? 0),
        ];
        $counts['total'] = array_sum($counts);

        return view('teacher.leaves.index', compact('schoolClass', 'leaveRequests', 'status', 'counts'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);

        $leaveRequest->load('student', 'reviewedBy');

        return view('teacher.leaves.show', compact('leaveRequest'));
    }

    public function review(ReviewLeaveRequest $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);
        abort_unless($leaveRequest->isPending(), 422, 'This request has already been reviewed.');

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $leaveRequest) {
            $leaveRequest->update([
                'status' => $validated['decision'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_note' => $validated['review_note'] ?? null,
            ]);

            if ($validated['decision'] === 'approved') {
                foreach ($leaveRequest->dateRange() as $date) {
                    Attendance::updateOrCreate(
                        ['student_id' => $leaveRequest->student_id, 'date' => $date],
                        ['status' => 'leave', 'marked_by' => auth()->id()]
                    );
                }
            }
        });

        return redirect()->route('teacher.leaves.show', $leaveRequest)->with('success', 'Leave request '.$validated['decision'].'.');
    }

    private function authorizeOwnership(LeaveRequest $leaveRequest): void
    {
        $schoolClass = auth()->user()->schoolClass;

        abort_unless($schoolClass && $leaveRequest->school_class_id === $schoolClass->id, 403, 'Access denied.');
    }
}
