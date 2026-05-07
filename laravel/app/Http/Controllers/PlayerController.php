<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Http\Requests\Player\PlayerRequest;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(){
        return inertia('player/player-index');
    }

    public function create(){
        return inertia('player/player-create', [
            'positions' => Position::all(['id', 'position_code']),
            'clubs' => Club::all(['id', 'clubname']),
        ]);
    }

    public function store(PlayerRequest $request){
        $validated = $request->validated();

        $player = Player::create(
            $validated
        );

        $player->positions()->attach($validated['position_ids']);


        return redirect()->route('player.index');
    }

    public function show($id){
        dd(Player::findOrFail($id));
    }

    public function edit($id){

    }

    public function update(Request $request, $id){

    }

    public function destroy($id){

    }
}
