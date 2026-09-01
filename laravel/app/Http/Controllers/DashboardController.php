<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return inertia('dashboard', [
            'playerQuickSearchClubs' => $user->playerQuickSearchClubs()->get(['id', 'clubname']),
        ]);
    }
}
