<?php

namespace App\Enums\Permission;

use App\Interfaces\PermissionsInterface;

enum EvaluationPermissions: int implements PermissionsInterface
{
    case Index = 7;
    case Search = 8;
}
