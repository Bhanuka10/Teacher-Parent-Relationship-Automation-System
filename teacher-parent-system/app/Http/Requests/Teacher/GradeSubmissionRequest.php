<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isTeacher(); }

    public function rules(): array
    {
        $maxMarks = $this->route('submission')?->homework?->max_marks ?? 1000;

        return [
            'marks'    => ['required', 'integer', 'min:0', "max:{$maxMarks}"],
            'feedback' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
