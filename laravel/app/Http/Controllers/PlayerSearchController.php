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
            'players' => $players->getData(),
        ]);
    }

    public function search(PlayerSearchRequest $request)
    {
        //TODO Implement server-side pagination
        $playerSearchDTO = new PlayerSearchDTO($request->validated());
        $playerSearchService = new PlayerSearchService();
        $players = $playerSearchService->searchPlayers($playerSearchDTO, ['positions:id,position_code', 'club:id,clubname'])->toArray();

        return response()->json($players);
    }
}
