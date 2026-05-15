<?php

namespace App\Http\Controllers;

use App\DTOs\PlayerSearchDTO;
use App\Http\Requests\Player\PlayerRequest;
use App\Http\Requests\Player\PlayerSearchRequest;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Services\PlayerSearchService;
use Emargareten\InertiaModal\Modal;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PlayerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:index,App\Models\Player', only: ['index']),
            new Middleware('can:search,App\Models\Player', only: ['search']),
            new Middleware('can:create,App\Models\Player', only: ['create', 'store']),
            new Middleware('can:view,player', only: ['show']),
            new Middleware('can:update,player', only: ['edit', 'update']),
            new Middleware('can:delete,player', only: ['destroy']),
        ];
    }

    public function index()
    {
        return inertia('player/player-index', [
            'positions' => Position::with('positionGroup:id,name')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::all(['id', 'clubname']),
        ]);
    }

    public function create()
    {
        return inertia('player/player-create', [
            'positions' => Position::with('positionGroup:id,name')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::all(['id', 'clubname']),
        ]);
    }

    public function store(PlayerRequest $request)
    {
        $validated = $request->validated();

        $player = Player::create($validated);
        $player->positions()->attach($validated['position_ids']);

        return redirect()->route('player.index');
    }

    public function show(Player $player)
    {
        return new Modal('player/player-show', [
            'player' => $player->load('positions', 'club'),
        ])->baseRoute('player.index');
    }

    public function edit(Player $player)
    {
        return inertia('player/player-edit', [
            'player' => $player->load('positions:id'),
            'positions' => Position::with('positionGroup:id,name')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::all(['id', 'clubname']),
        ]);
    }

    public function update(PlayerRequest $request, Player $player)
    {
        $validated = $request->validated();

        $player->update($validated);
        $player->positions()->sync($validated['position_ids']);

        return redirect()->route('player.index');
    }

    public function destroy(Player $player)
    {
        $player->delete();

        return redirect()->route('player.index');
    }

    public function search(PlayerSearchRequest $request)
    {
        //TODO Implement server-side pagination
        $playerSearchDTO = new PlayerSearchDTO($request->validated());
        $playerSearchService = new PlayerSearchService();
        $players = $playerSearchService->searchPlayers($playerSearchDTO, ['positions:id,position_code', 'club:id,clubname'])->toArray();

        return inertia('player/player-search', [
            'positions' => Position::with('positionGroup:id,name')->get(),
            'clubs' => Club::all(['id', 'clubname']),
            'players' => $players,
        ]);
    }
}
