<?php

namespace App\Services;

use App\DTOs\PlayerSearchDTO;
use App\Models\Player;
use Illuminate\Database\Eloquent\Collection;

class PlayerSearchService
{
    public function searchPlayers(PlayerSearchDTO $dto, array|string $with): Collection
    {
        return Player::with($with)
            ->when($dto->firstname, fn ($q) => $q->where('firstname', 'like', "%{$dto->firstname}%"))
            ->when($dto->lastname, fn ($q) => $q->where('lastname', 'like', "%{$dto->lastname}%"))
            ->when($dto->yearsOfBirth, fn ($q) => $q->whereIn('year_of_birth', $dto->yearsOfBirth))
            ->when($dto->heightFrom, fn ($q) => $q->where('height', '>=', $dto->heightFrom))
            ->when($dto->heightTo, fn ($q) => $q->where('height', '<=', $dto->heightTo))
            ->when($dto->strongFoots, fn ($q) => $q->whereIn('strong_foot', $dto->strongFoots))
            ->when($dto->clubIds, fn ($q) => $q->whereIn('club_id', $dto->clubIds))
            ->when($dto->positionIds, fn ($q) => $q->whereHas('positions', fn ($q) => $q->whereIn('positions.id', $dto->positionIds)))
            ->get();
    }
}
