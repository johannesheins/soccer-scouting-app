<?php

namespace App\Enums;

enum RecommendationEnum: int
{
    case firstTeamImmediately = 1;
    case firstTeamProspect = 2;
    case grassrootsFootball = 3;
    case continueMonitoring = 4;
}
