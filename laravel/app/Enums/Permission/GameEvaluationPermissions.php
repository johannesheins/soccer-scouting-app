<?php

namespace App\Enums\Permission;

use App\Interfaces\PermissionsInterface;

enum GameEvaluationPermissions: int implements PermissionsInterface
{
    case Create = 9;
    case View = 10;
    case ViewAll = 11;
    case Edit = 12;
    case EditAll = 13;
    case Destroy = 14;
    case DestroyAll = 15;
    case ViewCreator = 16;
}
