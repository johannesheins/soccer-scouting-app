<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'firstname' => ['string', 'max:255'],
            'lastname' => ['string', 'max:255'],
            'age' => ['integer'],
            'club_id' => ['integer', 'exists:clubs,id'],
            'position_ids' => ['integer', 'exists:positions,id'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
