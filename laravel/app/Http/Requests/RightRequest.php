<?php

namespace App\Http\Requests;

use App\Concerns\RightValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class RightRequest extends FormRequest
{
    use RightValidationRules;
    public function rules(): array
    {
        return $this->rightRules();
    }

    public function authorize(): bool
    {
        return true;
    }
}
