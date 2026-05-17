<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\EvaluationController;
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

    Route::get('evaluation/search', [EvaluationController::class, 'search'])->name('evaluation.search');
    Route::resource('evaluation', EvaluationController::class)->names('evaluation');

    Route::resource('club', ClubController::class)->names('club');
});

require __DIR__.'/settings.php';
require __DIR__.'/administration.php';
