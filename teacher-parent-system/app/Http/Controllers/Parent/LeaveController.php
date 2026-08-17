<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parent\StoreLeaveRequest;
use App\Http\Requests\Parent\UpdateLeaveRequest;
use App\Models\LeaveRequest;

class LeaveController extends Controller
{
    public function index()
    {
        $student = auth()->user()->students()->first();

        if (!$student) {
            return view('parent.leaves.index', ['student' => null, 'leaveRequests' => collect()]);
        }

        $leaveRequests = LeaveRequest::where('student_id', $student->id)
            ->latest()
            ->get();

        return view('parent.leaves.index', compact('student', 'leaveRequests'));
    }

    public function create()
    {
        $student = auth()->user()->students()->first();

        return view('parent.leaves.create', compact('student'));
    }

    public function store(StoreLeaveRequest $request)
    {
        $student = auth()->user()->students()->first();

        if (!$student) {
            return redirect()->route('parent.leaves.index')
                ->withErrors(['leave' => 'No student record is linked to this account.']);
        }

        $validated = $request->validated();

        LeaveRequest::create([
            'student_id' => $student->id,
            'school_class_id' => $student->school_class_id,
            'requested_by' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('parent.leaves.index')->with('success', 'Leave request submitted.');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);

        return view('parent.leaves.edit', compact('leaveRequest'));
    }

    public function update(UpdateLeaveRequest $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);

        $leaveRequest->update($request->validated());

        return redirect()->route('parent.leaves.index')->with('success', 'Leave request updated.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnership($leaveRequest);

        $leaveRequest->delete();

        return redirect()->route('parent.leaves.index')->with('success', 'Leave request cancelled.');
    }

    private function authorizeOwnership(LeaveRequest $leaveRequest): void
    {
        $student = auth()->user()->students()->first();

        abort_unless($student && $leaveRequest->student_id === $student->id, 403, 'Access denied.');
        abort_unless($leaveRequest->isPending(), 422, 'This request has already been reviewed and can no longer be changed.');
    }
}
