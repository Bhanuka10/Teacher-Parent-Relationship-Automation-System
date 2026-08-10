<?php

namespace App\Http\Requests\Admin;

use App\Models\GradingSchemeBand;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class StoreGradingBandRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'min_mark' => ['required', 'integer', 'min:0', 'max:100'],
            'max_mark' => ['required', 'integer', 'min:0', 'max:100', 'gte:min_mark'],
            'grade' => ['required', 'string', 'max:5'],
            'is_passing' => ['boolean'],
        ];
    }

    // Overlapping bands would make grading ambiguous (a mark could match two
    // bands) — the DB can't express range-overlap as a constraint, so it's
    // checked here against every other existing band.
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $min = (int) $this->input('min_mark');
            $max = (int) $this->input('max_mark');

            $overlaps = GradingSchemeBand::where('min_mark', '<=', $max)
                ->where('max_mark', '>=', $min)
                ->exists();

            if ($overlaps) {
                $validator->errors()->add('min_mark', 'This range overlaps an existing grading band.');
            }
        });
    }
}
