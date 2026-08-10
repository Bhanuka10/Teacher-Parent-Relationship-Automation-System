<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeworkRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'instructions'      => ['nullable', 'string', 'max:5000'],
            'due_at'            => ['nullable', 'date'],
            'duration_minutes'  => ['nullable', 'integer', 'min:1', 'max:600'],
        ];
    }
}
