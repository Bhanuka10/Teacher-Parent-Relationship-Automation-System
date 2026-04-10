<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:school_classes,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'teacher_id'  => ['nullable', 'exists:users,id'],
        ];
    }
}