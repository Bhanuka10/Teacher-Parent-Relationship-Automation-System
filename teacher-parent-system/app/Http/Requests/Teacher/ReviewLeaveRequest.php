<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class ReviewLeaveRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isTeacher(); }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
