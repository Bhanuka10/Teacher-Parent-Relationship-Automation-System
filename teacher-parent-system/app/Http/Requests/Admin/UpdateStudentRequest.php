<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        $studentId = $this->route('student')->id;
        return [
            'name'             => ['required', 'string', 'max:255'],
            'admission_number' => ['required', 'string', 'unique:students,admission_number,'.$studentId],
            'date_of_birth'    => ['nullable', 'date'],
            'gender'           => ['nullable', 'in:male,female,other'],
            'school_class_id'  => ['nullable', 'exists:school_classes,id'],
            'parent_id'        => ['nullable', 'exists:users,id'],
        ];
    }
}