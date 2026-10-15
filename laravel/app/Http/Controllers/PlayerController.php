<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Enums\Request\PlayerRequestNameEnum as Name;
use App\Http\Requests\Player\PlayerRequest;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use Emargareten\InertiaModal\Modal;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PlayerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:index,App\Models\Player', only: ['index']),
            new Middleware('can:create,App\Models\Player', only: ['create', 'store']),
            new Middleware('can:view,player', only: ['show']),
            new Middleware('can:update,player', only: ['edit', 'update']),
            new Middleware('can:delete,player', only: ['destroy']),
        ];
    }

    public function index()
    {
        return inertia('player/player-index', [
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
        ]);
    }

    public function create()
    {
        return inertia('player/player-create', [
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
        ]);
    }

    public function store(PlayerRequest $request)
    {
        self::storeApi($request);
        return redirect()->route('player.index');
    }

    public function storeApi(PlayerRequest $request)
    {
        $validated = $request->validated();

        $player = Player::create($validated);
        $player->positions()->attach($validated[reqN(Name::positionIds)] ?? []);

        $player->saveLog(AuditEvent::CREATED, null, $player->createSnapshot());

        return response()->json($player->load('positions', 'club'));
    }

    public function show(Player $player)
    {
        return new Modal('player/player-show', [
            'player' => $player->loadForPlayerView(),
        ])->baseRoute('player.index');
    }

    public function edit(Player $player)
    {
        return inertia('player/player-edit', [
            'player' => $player->load('positions:id'),
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
        ]);
    }

    public function update(PlayerRequest $request, Player $player)
    {
        $validated = $request->validated();

        $old = $player->createSnapshot();

        $player->update($validated);
        $player->positions()->sync($validated[reqN(Name::positionIds)] ?? []);

        $player->saveLog(AuditEvent::UPDATED, $old, $player->createSnapshot());

        return redirect()->route('player.index');
    }

    public function destroy(Player $player)
    {
        $old = $player->createSnapshot();
        $player->delete();

        $player->saveLog(AuditEvent::DELETED, $old, null);

        return redirect()->route('player.index');
    }
}
