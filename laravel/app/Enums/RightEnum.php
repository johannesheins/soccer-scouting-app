<?php

namespace App\Enums;

enum RightEnum: string
{
    case PlayerIndex  = 'player.index';
    case PlayerSearch  = 'player.search';
    case PlayerView   = 'player.view';
    case PlayerCreate = 'player.create';
    case PlayerEdit   = 'player.edit';
    case PlayerDelete = 'player.delete';
}
