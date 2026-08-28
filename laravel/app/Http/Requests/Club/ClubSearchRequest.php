<?php

namespace App\Http\Requests\Club;

use App\Concerns\ClubValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class ClubSearchRequest extends FormRequest
{
    use ClubValidationRules;
    public function rules(): array
    {
        return [
            'clubname' => ['nullable', 'string', 'max:255'],
            'zip_code' => $this->zipCodeRules('nullable'),
            'city' => $this->cityRules('nullable'),
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
