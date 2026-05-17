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

    case EvaluationIndex = 7;
    case EvaluationSearch = 8;
    case EvaluationCreate = 9;
    case EvaluationView = 10;
    case EvaluationViewAll = 11;
    case EvaluationEdit = 12;
    case EvaluationEditAll = 13;
    case EvaluationDestroy = 14;
    case EvaluationDestroyAll = 15;
}
