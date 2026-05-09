<?php

use App\Http\Controllers\ClubController;
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


    Route::resource('club', ClubController::class)->names('club');
});

Route::middleware(['auth', 'verified'])->group(function () { //TODO Add administration middleware
    Route::inertia('administration', 'administration/dashboard')->name('administration.dashboard');
});

require __DIR__.'/settings.php';
