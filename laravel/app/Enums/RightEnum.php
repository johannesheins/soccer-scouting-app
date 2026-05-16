<?php

namespace App\Enums;

enum RightEnum: int
{
    case PlayerIndex   = 1;
    case PlayerSearch  = 2;
    case PlayerCreate  = 3;
    case PlayerView    = 4;
    case PlayerEdit    = 5;
    case PlayerDestroy = 6;
}
