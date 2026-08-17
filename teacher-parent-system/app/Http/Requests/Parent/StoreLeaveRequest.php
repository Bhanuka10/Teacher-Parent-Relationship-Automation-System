<?php

namespace App\Http\Requests\Parent;

use App\Models\LeaveRequest;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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

    // A rejected request doesn't block resubmitting the same dates, but an
    // overlapping pending/approved one would create a confusing duplicate —
    // checked here since the DB can't express "no overlapping date range".
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $student = auth()->user()->students()->first();

            if (!$student || !$this->filled(['start_date', 'end_date'])) {
                return;
            }

            $overlaps = LeaveRequest::where('student_id', $student->id)
                ->whereIn('status', ['pending', 'approved'])
                ->where('start_date', '<=', $this->input('end_date'))
                ->where('end_date', '>=', $this->input('start_date'))
                ->exists();

            if ($overlaps) {
                $validator->errors()->add('start_date', 'This overlaps an existing pending or approved leave request.');
            }
        });
    }
}
