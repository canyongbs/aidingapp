<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use AidingApp\ServiceManagement\Http\Controllers\ServiceRequestFeedbackFormWidgetController;
use AidingApp\ServiceManagement\Http\Controllers\ServiceRequestFormWidgetController;
use AidingApp\ServiceManagement\Http\Controllers\ServiceRequestWidgetAssetController;
use AidingApp\ServiceManagement\Http\Middleware\EnsureServiceManagementFeatureIsActive;
use AidingApp\ServiceManagement\Http\Middleware\FeedbackManagementIsOn;
use AidingApp\ServiceManagement\Http\Middleware\ServiceRequestFormWidgetCors;
use AidingApp\ServiceManagement\Http\Middleware\ServiceRequestTypeFeedbackIsOn;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::middleware([
    'api',
    EncryptCookies::class,
    // TODO: This changes the feedback widget to check if Service Management is active. This was not done before, but should. Need to check if this breaks anything.
    EnsureServiceManagementFeatureIsActive::class,
    // TODO: Determine if this stateful middleware is needed
    // EnsureFrontendRequestsAreStateful::class,
])
    ->prefix('widgets/service-requests')
    ->name('widgets.service-requests.')
    ->group(function () {
        Route::prefix('forms')
            ->name('forms.')
            ->middleware([
                ServiceRequestFormWidgetCors::class,
            ])
            ->group(function () {
                Route::prefix('api/{serviceRequestForm}')
                    ->name('api.')
                    ->middleware([
                        EnsureSubmissibleIsEmbeddableAndAuthorized::class . ':serviceRequestForm',
                    ])
                    ->group(function () {
                        Route::get('', [ServiceRequestFormWidgetController::class, 'assets'])
                            ->name('assets');

                        Route::get('entry', [ServiceRequestFormWidgetController::class, 'view'])
                            ->name('entry');
                        Route::post('authenticate/request', [ServiceRequestFormWidgetController::class, 'requestAuthentication'])
                            ->middleware(['signed'])
                            ->name('request-authentication');
                        Route::post('authenticate/{authentication}', [ServiceRequestFormWidgetController::class, 'authenticate'])
                            ->middleware(['signed'])
                            ->name('authenticate');
                        Route::post('submit', [ServiceRequestFormWidgetController::class, 'store'])
                            ->middleware(['signed'])
                            ->name('submit');

                        // Handle preflight CORS requests for all routes in this group
                        // MUST remain the last route in this group
                        Route::options('/{any}', function (Request $request, ServiceRequestForm $serviceRequestForm) {
                            return response()->noContent();
                        })
                            ->where('any', '.*')
                            ->name('preflight');
                    });

                // This route MUST remain at /widgets/... in order to catch requests to asset files and return the correct headers
                // NGINX has been configured to route all requests for assets under /widgets to the application
                // This route is NOT duplicative of the one below it. The different routes are required to match to the specific CORS middleware
                Route::get('{file?}', ServiceRequestWidgetAssetController::class)
                    ->where('file', '(.*)')
                    ->name('asset');
            });

        Route::prefix('feedback')
            ->name('feedback.')
            ->middleware([
                // TODO: Add the CORS middleware for feedback widgets
            ])
            ->group(function () {
                Route::prefix('api/{serviceRequest}')
                    ->name('api.')
                    ->middleware([
                        FeedbackManagementIsOn::class,
                        ServiceRequestTypeFeedbackIsOn::class,
                    ])
                    ->group(function () {
                        // TODO: assets routes
                        Route::get('', [ServiceRequestFeedbackFormWidgetController::class, 'assets'])
                            ->name('assets');

                        // TODO: Fix this route and change it to "entry"
                        Route::get('/', [ServiceRequestFeedbackFormWidgetController::class, 'view'])
                            ->name('define');
                        Route::post('/submit', [ServiceRequestFeedbackFormWidgetController::class, 'store'])
                            ->middleware(['auth:sanctum'])
                            ->name('submit');

                        // TODO: preflight route
                    });

                // TODO: asset route
            });
    });
