<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('dashboard', [
            'userPinnedClubs' => auth()->user()->pinnedClubs()->get(['id', 'clubname']),
        ]);
    }
}
