<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'firstname'      => ['nullable', 'string', 'max:255'],
            'lastname'       => ['nullable', 'string', 'max:255'],
            'age'            => ['nullable', 'integer'],
            'club_ids'       => ['nullable', 'array'],
            'club_ids.*'     => ['integer', 'exists:clubs,id'],
            'position_ids'   => ['nullable', 'array'],
            'position_ids.*' => ['integer', 'exists:positions,id'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
