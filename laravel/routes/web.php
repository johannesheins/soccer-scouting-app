<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubSearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\Evaluations\GameEvaluationController;
use App\Http\Controllers\Evaluations\PlayerEvaluationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerSearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'password.renewed'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('player/search', [PlayerSearchController::class, 'index'])->name('player.search');
    Route::get('player/{player}/history', [PlayerController::class, 'history'])->name('player.history');
    Route::resource('player', PlayerController::class)->except('index')->names('player');

    Route::group(['prefix' => 'evaluation', 'as' => 'evaluation.'], function () {
        Route::get('search/{query?}', [EvaluationController::class, 'search'])->name('search');

        Route::get('game/{game_evaluation}/history', [GameEvaluationController::class, 'history'])
            ->name('game.history');
        Route::resource('game', GameEvaluationController::class)
            ->parameters(['game' => 'game_evaluation'])->except('index')
        ->names('game');

        Route::get('player/{player_evaluation}/history', [PlayerEvaluationController::class, 'history'])
            ->name('player.history');
        Route::resource('player', PlayerEvaluationController::class)
            ->parameters(['player' => 'player_evaluation'])
            ->except('index'
        )->names('player');
    });

    Route::get('club/search', [ClubSearchController::class, 'index'])->name('club.search');
    Route::get('club/{club}/history', [ClubController::class, 'history'])->name('club.history');
    Route::resource('club', ClubController::class)->except('index')->names('club');


    Route::group(['prefix' => 'api',], function () {
        Route::post('player', [PlayerController::class, 'storeApi'])->name('api.player.store');
        Route::get('player/search', [PlayerSearchController::class, 'search'])->name('api.player.search');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/administration.php';
