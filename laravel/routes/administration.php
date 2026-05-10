<?php

use App\Http\Controllers\Administration\RoleController;
use App\Http\Middleware\RequireAdministrator;

Route::middleware(['auth', 'verified', RequireAdministrator::class])->prefix('administration')->group(function () {
    Route::inertia('/', 'administration/dashboard')->name('administration');

    Route::resource('role', RoleController::class)->names('administration.role');
});
