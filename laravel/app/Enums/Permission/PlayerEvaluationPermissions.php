<?php

namespace App\Enums\Permission;

use App\Interfaces\PermissionsInterface;

enum PlayerEvaluationPermissions: int implements PermissionsInterface
{
    case Create = 23;
    case View = 24;
    case ViewAll = 25;
    case Edit = 26;
    case EditAll = 27;
    case Destroy = 28;
    case DestroyAll = 29;
    case ViewCreator = 30;
}
