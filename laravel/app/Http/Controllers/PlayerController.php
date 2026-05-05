<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(){
        return inertia('player/player-index');
    }

    public function create(){
        return inertia('player/player-create');
    }

    public function store(Request $request){

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
