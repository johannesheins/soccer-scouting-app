<?php

namespace App\Http\Controllers;

use App\DTOs\PlayerSearchDTO;
use App\Http\Requests\Player\PlayerSearchRequest;
use App\Models\Club;
use App\Models\Position;
use App\Services\PlayerSearchService;
use Emargareten\InertiaModal\Modal;

class PlayerSearchController extends Controller
{
    public function index(PlayerSearchRequest $request)
    {
        $players = self::search($request);

        return inertia('player/player-search', [
            'positions' => Position::with('positionGroup:id,name')->get(),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'players' => $players,
        ]);
    }

    public function modal(PlayerSearchRequest $request)
    {
        $players = self::search($request);

        return new Modal('player/player-search-modal', [
            'positions' => Position::with('positionGroup:id,name')->get(),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'players' => $players,
        ]);
    }

    private function search(PlayerSearchRequest $request)
    {
        //TODO Implement server-side pagination
        $playerSearchDTO = new PlayerSearchDTO($request->validated());
        $playerSearchService = new PlayerSearchService();
        return $playerSearchService->searchPlayers($playerSearchDTO, ['positions:id,position_code', 'club:id,clubname'])->toArray();
    }
}
