<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class PlayerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'year_of_birth' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'club_id' => 'required|exists:clubs,id',
            'position_ids' => 'required|array',
            'position_ids.*' => 'integer|exists:positions,id',
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
