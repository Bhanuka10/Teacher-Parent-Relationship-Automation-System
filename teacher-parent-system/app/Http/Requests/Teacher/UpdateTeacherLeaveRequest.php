<?php

namespace App\Http\Requests\Teacher;

use App\Models\TeacherLeaveRequest;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherLeaveRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isTeacher(); }

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
            $teacherLeaveRequest = $this->route('teacherLeaveRequest');

            if (!$this->filled(['start_date', 'end_date'])) {
                return;
            }

            $overlaps = TeacherLeaveRequest::where('teacher_id', auth()->id())
                ->whereIn('status', ['pending', 'approved'])
                ->where('id', '!=', $teacherLeaveRequest->id)
                ->where('start_date', '<=', $this->input('end_date'))
                ->where('end_date', '>=', $this->input('start_date'))
                ->exists();

            if ($overlaps) {
                $validator->errors()->add('start_date', 'This overlaps an existing pending or approved leave request.');
            }
        });
    }
}
