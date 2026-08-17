<?php

namespace App\Http\Requests\Parent;

use App\Models\LeaveRequest;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isParent(); }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $student = auth()->user()->students()->first();
            $leaveRequest = $this->route('leaveRequest');

            if (!$student || !$this->filled(['start_date', 'end_date'])) {
                return;
            }

            $overlaps = LeaveRequest::where('student_id', $student->id)
                ->whereIn('status', ['pending', 'approved'])
                ->where('id', '!=', $leaveRequest->id)
                ->where('start_date', '<=', $this->input('end_date'))
                ->where('end_date', '>=', $this->input('start_date'))
                ->exists();

            if ($overlaps) {
                $validator->errors()->add('start_date', 'This overlaps an existing pending or approved leave request.');
            }
        });
    }
}
