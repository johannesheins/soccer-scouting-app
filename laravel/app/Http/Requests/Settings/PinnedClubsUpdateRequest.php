<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ClubValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class PinnedClubsUpdateRequest extends FormRequest
{
    use ClubValidationRules;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'club_ids' => ['array', 'max:3'],
            'club_ids.*' => $this->clubIdRules(),
        ];
    }
}
