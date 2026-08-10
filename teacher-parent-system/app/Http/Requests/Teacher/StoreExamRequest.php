<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
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

            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*.name' => ['required', 'string', 'max:255'],
            'subjects.*.max_mark' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

    // Duplicate subject names within one exam would break rank/grade lookups by
    // name and confuse the marks grid — reject them here since rules() can't
    // express a "unique among siblings" constraint declaratively.
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $names = collect((array) $this->input('subjects', []))
                ->map(fn ($subject) => mb_strtolower(trim($subject['name'] ?? '')))
                ->filter();

            if ($names->count() !== $names->unique()->count()) {
                $validator->errors()->add('subjects', 'Subject names must be unique within an exam.');
            }
        });
    }
}
