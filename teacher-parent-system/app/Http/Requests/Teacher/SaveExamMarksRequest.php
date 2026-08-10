<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class SaveExamMarksRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'marks' => ['required', 'array'],
            'marks.*' => ['array'],
            'marks.*.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    // Each subject has its own configurable max_mark, so the ceiling per cell
    // can't be expressed as a static rule — checked here against the exam's
    // actual subjects instead.
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $exam = $this->route('exam');
            $subjects = $exam->subjects()->get()->keyBy('id');

            foreach ((array) $this->input('marks', []) as $studentId => $subjectMarks) {
                foreach ((array) $subjectMarks as $subjectId => $mark) {
                    if ($mark === null || $mark === '') {
                        continue;
                    }

                    $subject = $subjects->get((int) $subjectId);

                    if ($subject && (int) $mark > $subject->max_mark) {
                        $validator->errors()->add(
                            "marks.$studentId.$subjectId",
                            "Mark can't exceed {$subject->max_mark} for {$subject->name}."
                        );
                    }
                }
            }
        });
    }
}
