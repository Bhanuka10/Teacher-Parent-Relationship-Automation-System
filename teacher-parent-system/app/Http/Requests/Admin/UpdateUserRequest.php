<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        $user = $this->route('user');
        $classAssignable = in_array($user->role, ['teacher', 'parent'], true);

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'grade'    => [Rule::requiredIf($classAssignable), 'nullable', 'integer', 'between:1,12'],
            'section'  => [Rule::requiredIf($classAssignable), 'nullable', 'in:A,B,C,D,E'],
        ];
    }
}
