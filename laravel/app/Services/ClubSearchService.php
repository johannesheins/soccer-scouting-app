<?php

namespace App\Services;

use App\DTOs\ClubSearchDTO;
use App\Models\Club;
use Illuminate\Database\Eloquent\Collection;

class ClubSearchService
{
    public function searchClubs(ClubSearchDTO $dto, array|string $with = []): Collection
    {
        return Club::with($with)
            ->when($dto->clubname, fn ($q) => $q->where('clubname', 'like', "%{$dto->clubname}%"))
            ->when($dto->zipCode, fn ($q) => $q->where('zip_code', 'like', "%{$dto->zipCode}%"))
            ->when($dto->city, fn ($q) => $q->where('city', 'like', "%{$dto->city}%"))
            ->get();
    }
}
