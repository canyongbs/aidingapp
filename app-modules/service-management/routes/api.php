<?php

use AidingApp\ServiceManagement\Http\Controllers\Api\V1\ServiceRequests\ListServiceRequestsController;
use Illuminate\Support\Facades\Route;

Route::api(majorVersion: 1, routes: function () {
    Route::name('service-requests.')
        ->prefix('service-requests')
        ->group(function () {
            Route::get('/', ListServiceRequestsController::class)->name('index');
        });
});
