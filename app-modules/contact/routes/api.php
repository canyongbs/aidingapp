<?php

use AidingApp\Contact\Http\Controllers\Api\V1\Organizations\CreateOrganizationController;
use AidingApp\Contact\Http\Controllers\Api\V1\Organizations\ListOrganizationsController;
use AidingApp\Contact\Http\Controllers\Api\V1\Organizations\UpdateOrganizationController;
use AidingApp\Contact\Http\Controllers\Api\V1\Organizations\ViewOrganizationController;
use Illuminate\Support\Facades\Route;

Route::api(majorVersion: 1, routes: function (): void {
    Route::name('organizations.')
        ->prefix('organizations')
        ->group(function (): void {
            Route::get('/', ListOrganizationsController::class)->name('index');
            Route::post('/', CreateOrganizationController::class)->name('store');
            Route::get('/{organization}', ViewOrganizationController::class)->name('show');
            Route::patch('/{organization}', UpdateOrganizationController::class)->name('update');
        });
});
