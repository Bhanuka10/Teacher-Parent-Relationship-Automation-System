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

        return view('teacher.my-leave.index', compact('leaveRequests'));
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
