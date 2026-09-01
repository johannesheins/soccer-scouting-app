<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PlayerQuickSearchRequest;
use App\Models\Club;
use Inertia\Inertia;

class DashboardSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return inertia('settings/dashboard', [
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'playerQuickSearchUserClubs' => $user->playerQuickSearchClubs()->get(['id', 'clubname']),
        ]);
    }

    public function updatePlayerQuickSearchSettings(PlayerQuickSearchRequest $request)
    {
        $user = auth()->user();
        $user->playerQuickSearchClubs()->sync($request->validated('club_ids', []));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Pinned clubs updated.')]);

        return to_route('settings.dashboard.index');
    }
}
