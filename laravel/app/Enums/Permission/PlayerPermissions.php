<?php

namespace App\Enums\Permission;

use App\Interfaces\PermissionsInterface;

enum PlayerPermissions: int implements PermissionsInterface
{
    case Index   = 1;
    case Search  = 2;
    case Create  = 3;
    case View    = 4;
    case Edit    = 5;
    case Destroy = 6;
}
