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

use AidingApp\Ai\Http\Controllers\AssistantWidget\AuthenticateController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\AuthorizeBroadcastController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\BroadcastTypingController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\CheckServiceRequestConversationEligibilityController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\EvaluateServiceRequestAiResolutionController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\GenerateServiceRequestQuestionController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\GetServiceRequestFormController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\GetServiceRequestTypesController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\GetServiceRequestUploadUrlController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\GetWidgetConfigController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\ListConversationMessagesController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\RequestAuthenticationController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\RequestServiceRequestConversationController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\SendConversationMessageController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\SendMessageController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\ServeWidgetAssetController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\StoreServiceRequestController;
use AidingApp\Ai\Http\Middleware\AssistantWidgetCors;
use AidingApp\Ai\Http\Middleware\EnsureAssistantWidgetIsEmbeddableAndAuthorized;
use AidingApp\Portal\Http\Middleware\EnsureKnowledgeManagementPortalIsEnabled;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::middleware([
    'api',
    EncryptCookies::class,
    EnsureKnowledgeManagementPortalIsEnabled::class,
])
    ->prefix('widgets/assistant')
    ->name('widgets.assistant.')
    ->group(function () {
        Route::middleware([
            AssistantWidgetCors::class,
        ])
            ->group(function () {
                Route::prefix('api')
                    ->name('api.')
                    ->middleware([
                        EnsureFrontendRequestsAreStateful::class,
                        EnsureAssistantWidgetIsEmbeddableAndAuthorized::class,
                    ])
                    ->group(function () {
                        Route::get('config', GetWidgetConfigController::class)
                            ->name('config');

                        Route::post('authenticate/request', RequestAuthenticationController::class)
                            ->middleware(['signed:relative'])
                            ->name('authenticate.request');

                        Route::post('authenticate/{authentication}', AuthenticateController::class)
                            ->middleware(['signed:relative'])
                            ->name('authenticate');

                        Route::post('messages', SendMessageController::class)
                            ->name('messages');

                        Route::get('service-request-types', GetServiceRequestTypesController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request-types');

                        Route::get('service-request-form/{type}', GetServiceRequestFormController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request-form');

                        Route::get('service-request/upload-url', GetServiceRequestUploadUrlController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request.upload-url');

                        Route::post('service-request/{type}', StoreServiceRequestController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request.store');

                        Route::post('service-request/{type}/generate-question', GenerateServiceRequestQuestionController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request.generate-question');

                        Route::post('service-request/{type}/evaluate-ai-resolution', EvaluateServiceRequestAiResolutionController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request.evaluate-ai-resolution');

                        Route::get('service-request/{serviceRequest}/conversation/eligibility', CheckServiceRequestConversationEligibilityController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request.conversation.eligibility');

                        Route::post('service-request/{serviceRequest}/conversation', RequestServiceRequestConversationController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('service-request.conversation.request');

                        Route::get('conversations/{conversation}/messages', ListConversationMessagesController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('conversations.messages.index');

                        Route::post('conversations/{conversation}/messages', SendConversationMessageController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('conversations.messages.store');

                        Route::post('conversations/{conversation}/typing', BroadcastTypingController::class)
                            ->middleware(['auth:sanctum'])
                            ->name('conversations.typing');

                        Route::match(
                            ['GET', 'POST', 'HEAD'],
                            '/broadcasting/auth',
                            AuthorizeBroadcastController::class
                        )
                            ->name('broadcasting.auth');

                        Route::options('/{any}', function () {
                            return response()->noContent();
                        })
                            ->where('any', '.*')
                            ->name('preflight');
                    });

                Route::get('{file?}', ServeWidgetAssetController::class)
                    ->where('file', '(.*)')
                    ->name('asset');
            });
    });
