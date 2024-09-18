<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Support\Facades\Route;
use AidingApp\Portal\Livewire\RenderKnowledgeManagementPortal;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use AidingApp\Portal\Http\Middleware\EnsureKnowledgeManagementPortalIsEnabled;
use AidingApp\Portal\Http\Middleware\EnsureKnowledgeManagementPortalIsEmbeddableAndAuthorized;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalAuthenticateController;

Route::get('/portals/knowledge-management/{any?}', function ($any = null) {
    $url = $any ? '/portal/' . $any : '/portal';

    return redirect($url, 301);
})->where('any', '.*');

Route::prefix('portal')
    ->name('portal.')
    ->middleware([
        'web',
        EnsureFrontendRequestsAreStateful::class,
    ])
    ->group(function () {
        Route::middleware([
            EnsureKnowledgeManagementPortalIsEnabled::class,
            EnsureKnowledgeManagementPortalIsEmbeddableAndAuthorized::class,
        ])->group(function () {
            Route::post('/authenticate/{authentication}', KnowledgeManagementPortalAuthenticateController::class)
                ->middleware(['signed:relative', EnsureFrontendRequestsAreStateful::class])
                ->name('authenticate');

            Route::get('/', RenderKnowledgeManagementPortal::class)
                ->name('show');
            Route::get('/categories/{category}', RenderKnowledgeManagementPortal::class)
                ->name('category.show');
            Route::get('/categories/{category}/articles/{article}', RenderKnowledgeManagementPortal::class)
                ->name('article.show');
            Route::get('/service-request-type/select', RenderKnowledgeManagementPortal::class)
                ->name('service-request.show');
            Route::get('/service-request/create/{type}', RenderKnowledgeManagementPortal::class)
                ->name('service-request-type.show');
            Route::get('/service-request/{serviceRequest}', RenderKnowledgeManagementPortal::class)
                ->name('service-request.show');
        });
    });
