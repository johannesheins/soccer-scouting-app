<?php

use App\Http\Controllers\Administration\UserController;
use App\Http\Controllers\Administration\UserGroupController;
use App\Http\Middleware\RequireAdministrator;

Route::middleware(['auth', 'verified', RequireAdministrator::class])->prefix('administration')->group(function () {
    Route::inertia('/', 'administration/dashboard')->name('administration');

    Route::resource('user-group', UserGroupController::class)->names('administration.user-group');
    Route::resource('user', UserController::class)->names('administration.user');
});
