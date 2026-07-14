<?php

namespace App\Enums\Request;

enum PlayerRequestNameEnum: string implements RequestNameEnumInterface
{
    case playerId = 'player_id';
    case firstname = 'firstname';
    case lastname = 'lastname';
    case yearOfBirth = 'year_of_birth';
    case height = 'height';
    case strongFoot = 'strong_foot';
    case clubId = 'club_id';
    case positionIds = 'position_ids';
}
