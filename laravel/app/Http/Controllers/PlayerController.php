<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
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

    public function store(Request $request){
        dd($request->all());
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
