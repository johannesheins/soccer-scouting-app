<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Enums\Request\PlayerRequestNameEnum as Name;
use App\Http\Requests\Player\PlayerRequest;
use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Player;
use App\Models\Position;
use App\Http\Responses\Modal;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class PlayerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:create,App\Models\Player', only: ['create', 'store']),
            new Middleware('can:view,player', only: ['show']),
            new Middleware('can:viewHistory,player', only: ['history']),
            new Middleware('can:update,player', only: ['edit', 'update']),
            new Middleware('can:delete,player', only: ['destroy']),
        ];
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Player was created.')]);
        return to_route('player.search');
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
            'player' => $player->loadForView(),
        ])->baseRoute('player.search');
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

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('Player was updated.')])->back();
    }

    public function destroy(Player $player)
    {
        $old = $player->createSnapshot();
        $player->delete();

        $player->saveLog(AuditEvent::DELETED, $old, null);

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('Player was deleted.')])->back();
    }

    public function history(Player $player)
    {
        $positions = Position::all()->keyBy('id');
        $clubs = Club::all()->keyBy('id');

        $versions = $player->auditLogs()->with('user')->orderBy('id')->get()
            ->map(function (AuditLog $log) use ($player, $positions, $clubs): array {
                $values = $log->new_values ?? $log->old_values;

                $version = $player->newInstance([], true);
                $version->setRawAttributes(Arr::except($values, 'positions'), true);
                $version->setRelation('positions', $positions->only($values['positions'] ?? [])->values());
                $version->setRelation('club', $clubs->get($version->club_id));

                return [
                    'id' => $log->id,
                    'event' => $log->event,
                    'created_at' => $log->created_at,
                    'user' => $log->user,
                    'changes' => $log->changedFields(),
                    'new_values' => $log->new_values,
                    'player' => $version,
                ];
            })->all();

        return new Modal('player/player-view-history', [
            'player' => $player->loadForView(),
            'versions' => $versions,
        ])->baseRoute('player.search');
    }
}
