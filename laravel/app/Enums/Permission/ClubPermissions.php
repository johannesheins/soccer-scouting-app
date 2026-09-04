<?php

namespace App\Enums\Permission;

use App\Interfaces\PermissionsInterface;

enum ClubPermissions: int implements PermissionsInterface
{
    case Index = 17;
    case Search = 18;
    case Create = 19;
    case View = 20;
    case Edit = 21;
    case Destroy = 22;
}
