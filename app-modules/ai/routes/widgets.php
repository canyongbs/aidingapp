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

use AidingApp\Ai\Http\Controllers\AssistantWidget\AssistantBroadcastController;
use AidingApp\Ai\Http\Controllers\AssistantWidget\AssistantWidgetController;
use AidingApp\Ai\Http\Middleware\AssistantWidgetAuthorization;
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
                        Route::get('config', [AssistantWidgetController::class, 'config'])
                            ->name('config');

                        Route::post('messages', [AssistantWidgetController::class, 'sendMessage'])
                            ->name('messages');

                        Route::match(
                            ['GET', 'POST', 'HEAD'],
                            '/broadcasting/auth',
                            [AssistantBroadcastController::class, 'auth']
                        )
                            ->middleware([AssistantWidgetAuthorization::class])
                            ->name('broadcasting.auth');

                        Route::options('/{any}', function () {
                            return response()->noContent();
                        })
                            ->where('any', '.*')
                            ->name('preflight');
                    });

                Route::get('{file?}', [AssistantWidgetController::class, 'asset'])
                    ->where('file', '(.*)')
                    ->name('asset');
            });
    });
