<?php

namespace App\Http\Requests\Club;

use App\Concerns\ClubValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class ClubRequest extends FormRequest
{
    use ClubValidationRules;
    public function rules(): array
    {
        return $this->clubRules();
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
