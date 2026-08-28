<?php

namespace App\Enums\Request;

enum ClubRequestNameEnum: string implements RequestNameEnumInterface
{
    case clubname = 'clubname';
    case zipCode = 'zip_code';
    case city = 'city';
}
