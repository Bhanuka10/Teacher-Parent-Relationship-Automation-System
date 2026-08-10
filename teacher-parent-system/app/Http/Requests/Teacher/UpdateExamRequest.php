<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'academic_year' => ['required', 'string', 'max:9', 'regex:/^\d{4}\/\d{4}$/'],
            'term' => ['required', 'integer', 'in:1,2,3'],
            'name' => ['required', 'string', 'max:255'],
            'exam_date' => ['required', 'date'],
            'term_start_date' => ['required', 'date'],
            'term_end_date' => ['required', 'date', 'after_or_equal:term_start_date'],

            'new_subjects' => ['nullable', 'array'],
            'new_subjects.*.name' => ['required_with:new_subjects', 'string', 'max:255'],
            'new_subjects.*.max_mark' => ['required_with:new_subjects', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
