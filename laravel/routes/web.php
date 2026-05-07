<?php

use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::get('player/search', [PlayerController::class, 'search'])->name('player.search');
    Route::resource('player', PlayerController::class)->names('player');
});

require __DIR__.'/settings.php';
