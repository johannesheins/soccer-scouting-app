<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Http\Requests\Club\ClubRequest;
use App\Models\AuditLog;
use App\Models\Club;
use Emargareten\InertiaModal\Modal;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ClubController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:index,App\Models\Club', only: ['index']),
            new Middleware('can:search,App\Models\Club', only: ['search']),
            new Middleware('can:create,App\Models\Club', only: ['create', 'store']),
            new Middleware('can:view,club', only: ['show']),
            new Middleware('can:viewHistory,club', only: ['history']),
            new Middleware('can:update,club', only: ['edit', 'update']),
            new Middleware('can:delete,club', only: ['destroy']),
        ];
    }

    public function index()
    {
        return inertia('club/club-index');
    }

    public function create()
    {
        return inertia('club/club-create');
    }

    public function store(ClubRequest $request)
    {
        $club = Club::create($request->validated());

        $club->saveLog(AuditEvent::CREATED, null, $club->createSnapshot());

        return redirect()->route('club.index');
    }

    public function show(Club $club)
    {
        return new Modal('club/club-show', [
            'club' => $club,
        ])->baseRoute('club.index');
    }

    public function edit(Club $club)
    {
        return inertia('club/club-edit', [
            'club' => $club,
        ]);
    }

    public function update(ClubRequest $request, Club $club)
    {
        $old = $club->createSnapshot();

        $club->update($request->validated());

        $club->saveLog(AuditEvent::UPDATED, $old, $club->createSnapshot());

        return redirect()->route('club.index');
    }

    public function destroy(Club $club)
    {
        $old = $club->createSnapshot();
        $club->delete();

        $club->saveLog(AuditEvent::DELETED, $old, null);

        return redirect()->route('club.index');
    }

    public function history(Club $club)
    {
        $versions = $club->auditLogs()->with('user')->orderBy('id')->get()
            ->map(function (AuditLog $log) use ($club): array {
                $version = $club->newInstance([], true);
                $version->setRawAttributes($log->new_values ?? $log->old_values, true);

                return [
                    'id' => $log->id,
                    'event' => $log->event,
                    'created_at' => $log->created_at,
                    'user' => $log->user,
                    'changes' => $log->changedFields(),
                    'new_values' => $log->new_values,
                    'club' => $version,
                ];
            })->all();

        return new Modal('club/club-view-history', [
            'club' => $club,
            'versions' => $versions,
        ])->baseRoute('club.index');
    }
}
