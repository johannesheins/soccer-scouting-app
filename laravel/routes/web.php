<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubSearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\Evaluations\GameEvaluationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerSearchController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('player/search', [PlayerSearchController::class, 'index'])->name('player.search');
    Route::resource('player', PlayerController::class)->names('player');

    Route::group(['prefix' => 'evaluation', 'as' => 'evaluation.'], function () {
        Route::get('search/{query?}', [EvaluationController::class, 'search'])->name('search');
        Route::get('', [EvaluationController::class, 'index'])->name('index');
        Route::resource('game', GameEvaluationController::class)->parameters(['game' => 'game_evaluation'])->names('game');
    });

    Route::get('club/search', [ClubSearchController::class, 'index'])->name('club.search');
    Route::resource('club', ClubController::class)->names('club');


    Route::group(['prefix' => 'api',], function () {
        Route::post('player', [PlayerController::class, 'storeApi'])->name('api.player.store');
        Route::get('player/search', [PlayerSearchController::class, 'search'])->name('api.player.search');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/administration.php';
