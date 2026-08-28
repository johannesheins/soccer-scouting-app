<?php

namespace App\Http\Controllers;

use App\Http\Requests\Club\ClubRequest;
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
        Club::create($request->validated());

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
        $club->update($request->validated());

        return redirect()->route('club.index');
    }

    public function destroy(Club $club)
    {
        $club->delete();

        return redirect()->route('club.index');
    }
}
