<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerSearchController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::get('player/search', [PlayerSearchController::class, 'index'])->name('player.search');
    Route::resource('player', PlayerController::class)->names('player');

    Route::get('evaluation/search', [EvaluationController::class, 'search'])->name('evaluation.search');
    Route::resource('evaluation', EvaluationController::class)->names('evaluation');

    Route::resource('club', ClubController::class)->names('club');


    Route::group(['prefix' => 'api',], function () {
        Route::get('player/search', [PlayerSearchController::class, 'search'])->name('api.player.search');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/administration.php';
