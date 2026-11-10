<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\ServiceManagement\Http\Controllers\Api\V1\Advisories\ListAdvisoriesController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\Advisories\ViewAdvisoryController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisorySeverities\CreateAdvisorySeverityController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisorySeverities\DeleteAdvisorySeverityController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisorySeverities\ListAdvisorySeveritiesController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisorySeverities\UpdateAdvisorySeverityController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisorySeverities\ViewAdvisorySeverityController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisoryStatuses\CreateAdvisoryStatusController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisoryStatuses\DeleteAdvisoryStatusController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisoryStatuses\ListAdvisoryStatusesController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisoryStatuses\UpdateAdvisoryStatusController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisoryStatuses\ViewAdvisoryStatusController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\ServiceRequests\CreateServiceRequestUpdateController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\ServiceRequests\ListServiceRequestsController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\ServiceRequests\UpdateServiceRequestController;
use AidingApp\ServiceManagement\Http\Controllers\Api\V1\ServiceRequests\ViewServiceRequestController;
use Illuminate\Support\Facades\Route;

Route::api(majorVersion: 1, routes: function (): void {
    Route::name('advisories.')
        ->prefix('advisories')
        ->group(function (): void {
            Route::get('/', ListAdvisoriesController::class)->name('index');
            Route::get('/{advisory}', ViewAdvisoryController::class)->name('show');
        });

    Route::name('advisory-severities.')
        ->prefix('advisory-severities')
        ->group(function (): void {
            Route::get('/', ListAdvisorySeveritiesController::class)->name('index');
            Route::post('/', CreateAdvisorySeverityController::class)->name('store');
            Route::get('/{advisorySeverity}', ViewAdvisorySeverityController::class)->name('show');
            Route::patch('/{advisorySeverity}', UpdateAdvisorySeverityController::class)->name('update');
            Route::delete('/{advisorySeverity}', DeleteAdvisorySeverityController::class)->name('delete');
        });

    Route::name('advisory-statuses.')
        ->prefix('advisory-statuses')
        ->group(function (): void {
            Route::get('/', ListAdvisoryStatusesController::class)->name('index');
            Route::post('/', CreateAdvisoryStatusController::class)->name('store');
            Route::get('/{advisoryStatus}', ViewAdvisoryStatusController::class)->name('show');
            Route::patch('/{advisoryStatus}', UpdateAdvisoryStatusController::class)->name('update');
            Route::delete('/{advisoryStatus}', DeleteAdvisoryStatusController::class)->name('delete');
        });

    Route::name('service-requests.')
        ->prefix('service-requests')
        ->group(function (): void {
            Route::get('/', ListServiceRequestsController::class)->name('index');
            Route::get('/{serviceRequest}', ViewServiceRequestController::class)->name('show');
            Route::patch('/{serviceRequest}', UpdateServiceRequestController::class)->name('update');
            Route::post('/{serviceRequest}/updates', CreateServiceRequestUpdateController::class)->name('updates.store');
        });
});
