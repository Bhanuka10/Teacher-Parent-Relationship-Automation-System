<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role'     => ['required', 'in:teacher,parent'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'grade'    => ['nullable', 'integer', 'between:1,12', 'required_if:role,teacher,parent'],
            'section'  => ['nullable', 'in:A,B,C,D,E', 'required_if:role,teacher,parent'],
        ];
    }
}