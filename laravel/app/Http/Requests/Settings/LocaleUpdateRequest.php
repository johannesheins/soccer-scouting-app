<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocaleUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', Rule::in(array_keys(config('app.available_locales')))],
        ];
    }
}
