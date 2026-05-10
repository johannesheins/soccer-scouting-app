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

class PlayerController extends Controller
{
    public function index()
    {
        return inertia('player/player-dashboard');
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

    public function show($id)
    {
        return new Modal('player/player-show', [
            'player' => Player::findOrFail($id)->load('positions', 'club'),
        ])->baseRoute('player.index');
    }

    public function edit($id)
    {
        return inertia('player/player-edit', [
            'player' => Player::findOrFail($id)->load('positions:id'),
            'positions' => Position::with('positionGroup:id,name')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::all(['id', 'clubname']),
        ]);
    }

    public function update(PlayerRequest $request, $id)
    {
        $validated = $request->validated();

        $player = Player::findOrFail($id);
        $player->update($validated);
        $player->positions()->sync($validated['position_ids']);

        return redirect()->route('player.index');
    }

    public function destroy($id)
    {
        Player::findOrFail($id)->delete();

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
