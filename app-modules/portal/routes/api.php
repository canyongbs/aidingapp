<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\AssetManagementPortalController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\EvaluateServiceRequestAiResolutionController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\GenerateServiceRequestQuestionsController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\GetKnowledgeManagementPortalTagsController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\GetServiceRequestFormController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\GetServiceRequestsController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\GetServiceRequestUploadUrl;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\IncidentController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalArticleController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalAuthenticateController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalCategoryController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalLogoutController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalRegisterController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalRequestAuthenticationController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalResourcesController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\KnowledgeManagementPortalSearchController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\LicenseManagementPortalController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\ServiceMonitorStatusController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\ServiceRequestTypesController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\StoreKnowledgeBaseArticleVoteController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\StoreServiceRequestController;
use AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal\StoreServiceRequestUpdateController;
use AidingApp\Portal\Http\Middleware\EnsureKnowledgeManagementPortalIsEmbeddableAndAuthorized;
use AidingApp\Portal\Http\Middleware\EnsureKnowledgeManagementPortalIsEnabled;
use AidingApp\Portal\Http\Routing\ArticleShowMissingHandler;
use AidingApp\Portal\Http\Routing\CategoryShowMissingHandler;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpFoundation\Response;

Route::prefix('api')
    ->name('api.')
    ->middleware([
        'api',
        EnsureFrontendRequestsAreStateful::class,
        EnsureKnowledgeManagementPortalIsEnabled::class,
        EnsureKnowledgeManagementPortalIsEmbeddableAndAuthorized::class,
    ])
    ->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/user', fn () => auth('contact')->user() ?? response()->json(status: Response::HTTP_UNAUTHORIZED))
                ->name('user.auth-check');
        });

        Route::prefix('portal')
            ->name('portal.')
            ->group(function () {
                Route::get('/', [KnowledgeManagementPortalController::class, 'show'])
                    ->name('define');

                Route::get('/resources', KnowledgeManagementPortalResourcesController::class)
                    ->name('resources');

                Route::post('/authenticate/request', KnowledgeManagementPortalRequestAuthenticationController::class)
                    ->middleware(['signed:relative'])
                    ->name('request-authentication');

                Route::post('/authenticate/{authentication}', KnowledgeManagementPortalAuthenticateController::class)
                    ->middleware(['signed:relative', EnsureFrontendRequestsAreStateful::class])
                    ->name('authenticate.embedded');

                Route::post('/register/{authentication}', KnowledgeManagementPortalRegisterController::class)
                    ->middleware(['signed:relative', EnsureFrontendRequestsAreStateful::class])
                    ->name('authenticate.register.embedded');

                Route::post('/logout', KnowledgeManagementPortalLogoutController::class)
                    ->name('logout');

                Route::post('/search', [KnowledgeManagementPortalSearchController::class, 'get'])
                    ->name('search');

                Route::get('/categories', [KnowledgeManagementPortalCategoryController::class, 'index'])
                    ->name('category.index');

                Route::get('/categories/{category:slug}', [KnowledgeManagementPortalCategoryController::class, 'show'])
                    ->name('category.show')
                    ->missing((new CategoryShowMissingHandler())(...));

                Route::get('/categories/{category:slug}/articles/{article}', [KnowledgeManagementPortalArticleController::class, 'show'])
                    ->name('article.show')
                    ->missing((new ArticleShowMissingHandler())(...));

                Route::get('/service-request-type/select', [ServiceRequestTypesController::class, 'index'])
                    ->name('service-request-type.index');

                Route::get('/service-requests', [GetServiceRequestsController::class, 'index'])
                    ->name('service-request.index');

                Route::get('/service-request/create/{type}', GetServiceRequestFormController::class)
                    ->name('service-request.create');

                Route::post('/service-request/create/{type}', StoreServiceRequestController::class)
                    ->middleware(['auth:sanctum'])
                    ->name('service-request.store');

                Route::post('/service-request/create/{type}/generate-questions', GenerateServiceRequestQuestionsController::class)
                    ->middleware(['auth:sanctum'])
                    ->name('service-request.generate-questions');

                Route::post('/service-request/create/{type}/evaluate-ai-resolution', EvaluateServiceRequestAiResolutionController::class)
                    ->middleware(['auth:sanctum'])
                    ->name('service-request.evaluate-ai-resolution');

                Route::get('/service-request/request-upload-url', GetServiceRequestUploadUrl::class)
                    ->middleware(['auth:sanctum'])
                    ->name('service-request.request-upload-url');

                Route::get('/tags', GetKnowledgeManagementPortalTagsController::class)
                    ->name('tags.index');
                Route::get('/service-request/{serviceRequest}', [GetServiceRequestsController::class, 'show'])
                    ->name('service-request.show');
                Route::post('/service-request-update/store', StoreServiceRequestUpdateController::class)
                    ->name('service-request-update.storeServiceRequestUpdate');
                Route::post('/knowledge_base_article_vote/store', StoreKnowledgeBaseArticleVoteController::class)
                    ->name('knowledge_base_article_vote.storeKnowledgeBaseArticleVote');

                Route::get('/product-licenses', LicenseManagementPortalController::class)
                    ->middleware(['auth:sanctum'])
                    ->name('product.licenses');

                Route::get('/incidents', IncidentController::class)
                    ->middleware(['auth:sanctum'])
                    ->name('incidents');
                Route::get('/status', ServiceMonitorStatusController::class)
                    ->middleware(['auth:sanctum'])
                    ->name('status');
                Route::get('/assets', AssetManagementPortalController::class)
                    ->middleware(['auth:sanctum'])
                    ->name('assets.index');
            });
    });
