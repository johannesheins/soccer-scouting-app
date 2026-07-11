<?php

namespace App\Http\Requests\Player;

use App\Concerns\PlayerValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class PlayerRequest extends FormRequest
{
    use PlayerValidationRules;
    public function rules(): array
    {
        return $this->playerRules();
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
