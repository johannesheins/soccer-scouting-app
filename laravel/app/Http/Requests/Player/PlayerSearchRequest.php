<?php

namespace App\Http\Requests\Player;

use App\Enums\FootEnum;
use Illuminate\Foundation\Http\FormRequest;

class PlayerSearchRequest extends FormRequest
{
    public function rules(): array
    {
        $footCases = implode(',', array_column(FootEnum::cases(), 'value'));
        return [
            'firstname' => ['nullable', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],

            'club_ids' => ['nullable', 'array'],
            'club_ids.*' => ['integer', 'exists:clubs,id'],

            'years_of_birth' => ['nullable', 'array'],
            'years_of_birth.*' => ['nullable', 'string', 'regex:/^\d{4}\/\d{4}$/'],

            'height_from' => ['nullable', 'integer'],
            'height_to' => ['nullable', 'integer'],

            'strong_foots' => ['nullable', 'array'],
            'strong_foots.*' => ['required', 'string', "in:$footCases"],

            'position_ids' => ['nullable', 'array'],
            'position_ids.*' => ['integer', 'exists:positions,id'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
