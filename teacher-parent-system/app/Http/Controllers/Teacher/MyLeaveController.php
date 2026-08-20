<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherLeaveRequest;
use App\Http\Requests\Teacher\UpdateTeacherLeaveRequest;
use App\Models\TeacherLeaveRequest;

class MyLeaveController extends Controller
{
    public function index()
    {
        $leaveRequests = TeacherLeaveRequest::where('teacher_id', auth()->id())
            ->latest()
            ->get();

        $statusCounts = TeacherLeaveRequest::where('teacher_id', auth()->id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'pending'  => (int) ($statusCounts['pending'] ?? 0),
            'approved' => (int) ($statusCounts['approved'] ?? 0),
            'rejected' => (int) ($statusCounts['rejected'] ?? 0),
        ];
        $counts['total'] = array_sum($counts);

        return view('teacher.my-leave.index', compact('leaveRequests', 'counts'));
    }

    public function create()
    {
        return view('teacher.my-leave.create');
    }

    public function store(StoreTeacherLeaveRequest $request)
    {
        $validated = $request->validated();

        TeacherLeaveRequest::create([
            'teacher_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('teacher.my-leave.index')->with('success', 'Leave request submitted.');
    }

    public function edit(TeacherLeaveRequest $teacherLeaveRequest)
    {
        $this->authorizeOwnership($teacherLeaveRequest);

        return view('teacher.my-leave.edit', compact('teacherLeaveRequest'));
    }

    public function update(UpdateTeacherLeaveRequest $request, TeacherLeaveRequest $teacherLeaveRequest)
    {
        $this->authorizeOwnership($teacherLeaveRequest);

        $teacherLeaveRequest->update($request->validated());

        return redirect()->route('teacher.my-leave.index')->with('success', 'Leave request updated.');
    }

    public function destroy(TeacherLeaveRequest $teacherLeaveRequest)
    {
        $this->authorizeOwnership($teacherLeaveRequest);

        $teacherLeaveRequest->delete();

        return redirect()->route('teacher.my-leave.index')->with('success', 'Leave request cancelled.');
    }

    private function authorizeOwnership(TeacherLeaveRequest $teacherLeaveRequest): void
    {
        abort_unless($teacherLeaveRequest->teacher_id === auth()->id(), 403, 'Access denied.');
        abort_unless($teacherLeaveRequest->isPending(), 422, 'This request has already been reviewed and can no longer be changed.');
    }
}
