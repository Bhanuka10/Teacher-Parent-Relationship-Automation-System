<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomeworkRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'type'         => ['required', 'in:file,quiz'],
            'title'        => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'due_at'       => ['nullable', 'date'],
            // For quizzes max_marks is computed server-side from the sum of
            // question marks, so it's only required when type=file.
            'max_marks'    => ['required_if:type,file', 'nullable', 'integer', 'min:1', 'max:1000'],

            'allowed_file_types'   => ['required_if:type,file', 'array', 'min:1'],
            'allowed_file_types.*' => ['in:pdf,pptx,video,png'],

            'duration_minutes' => ['required_if:type,quiz', 'nullable', 'integer', 'min:1', 'max:600'],
            'questions'                    => ['required_if:type,quiz', 'array', 'min:1'],
            'questions.*.type'             => ['required_if:type,quiz', 'in:mcq,writing'],
            'questions.*.question'         => ['required', 'string', 'max:2000'],
            'questions.*.marks'            => ['required', 'integer', 'min:1', 'max:100'],
            'questions.*.options'          => ['required_if:questions.*.type,mcq', 'array'],
            'questions.*.options.*.text'   => ['required_with:questions.*.options', 'string', 'max:500'],
            'questions.*.options.*.is_correct' => ['nullable', 'boolean'],
        ];
    }

    // At least one correct option per MCQ question — the rules() array above
    // can't express a "one of these siblings must be true" constraint, so it
    // needs a closure check here instead of a declarative rule.
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            if ($this->input('type') !== 'quiz') {
                return;
            }

            foreach ((array) $this->input('questions', []) as $index => $question) {
                if (($question['type'] ?? null) !== 'mcq') {
                    continue;
                }

                $options = $question['options'] ?? [];
                $hasCorrect = collect($options)->contains(fn ($option) => !empty($option['is_correct']));
                $hasEnoughOptions = count($options) >= 2;

                if (!$hasEnoughOptions) {
                    $validator->errors()->add("questions.$index.options", 'Add at least two options for this question.');
                }

                if (!$hasCorrect) {
                    $validator->errors()->add("questions.$index.options", 'Mark at least one option as correct.');
                }
            }
        });
    }
}
