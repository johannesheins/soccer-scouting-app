<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerSearchRequest;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Http\Requests\Player\PlayerRequest;

class PlayerController extends Controller
{
    public function index(){
        return inertia('player/player-index');
    }

    public function create(){
        return inertia('player/player-create', [
            'positions' => Position::with('positionGroup:id,name')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::all(['id', 'clubname']),
        ]);
    }

    public function store(PlayerRequest $request){
        $validated = $request->validated();

        $player = Player::create($validated);
        $player->positions()->attach($validated['position_ids']);

        return redirect()->route('player.index');
    }

    public function show($id){
        dd(Player::findOrFail($id));
    }

    public function edit($id){
        return inertia('player/player-edit', [
            'player' => Player::findOrFail($id)->load('positions:id'),
            'positions' => Position::with('positionGroup:id,name')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::all(['id', 'clubname']),
        ]);
    }

    public function update(PlayerRequest $request, $id){
        $validated = $request->validated();

        $player = Player::findOrFail($id);
        $player->update($validated);
        $player->positions()->sync($validated['position_ids']);

        return redirect()->route('player.index');
    }

    public function destroy($id){

    }

    public function search(PlayerSearchRequest $request){
        $validated = $request->validated();

        $players = Player::all(); //TODO Implement Search in separate file

        return inertia('player/player-search', [
            'positions' => Position::with('positionGroup:id,name')->get(),
            'clubs' => Club::all(['id', 'clubname']),
            'players' => $players,
        ]);
    }
}
