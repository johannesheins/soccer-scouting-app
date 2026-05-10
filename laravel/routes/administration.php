<?php

use App\Http\Middleware\RequireAdministrator;

Route::middleware(['auth', 'verified', RequireAdministrator::class])->group(function () {
    Route::inertia('administration', 'administration/dashboard')->name('administration');
});
