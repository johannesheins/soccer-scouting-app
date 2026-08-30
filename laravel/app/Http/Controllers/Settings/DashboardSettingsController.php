<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PinnedClubsUpdateRequest;
use App\Models\Club;
use Inertia\Inertia;

class DashboardSettingsController extends Controller
{
    public function index()
    {
        return inertia('settings/dashboard', [
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'userPinnedClubs' => auth()->user()->pinnedClubs()->get(['id', 'clubname']),
        ]);
    }

    public function updatePinnedClubs(PinnedClubsUpdateRequest $request)
    {
        auth()->user()->pinnedClubs()->sync($request->validated('club_ids', []));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Pinned clubs updated.')]);

        return to_route('settings.dashboard.index');
    }
}
