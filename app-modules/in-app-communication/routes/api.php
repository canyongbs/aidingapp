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

use AidingApp\InAppCommunication\Http\Controllers\Conversations\AddParticipantController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\CreateConversationController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\JoinChannelController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\LeaveConversationController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\ListConversationsController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\ListPublicChannelsController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\MarkAsReadController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\RemoveParticipantController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\ShowConversationController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\UpdateConversationController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\UpdateParticipantController;
use AidingApp\InAppCommunication\Http\Controllers\Conversations\UpdateSettingsController;
use AidingApp\InAppCommunication\Http\Controllers\Messages\BroadcastTypingController;
use AidingApp\InAppCommunication\Http\Controllers\Messages\CreateMessageController;
use AidingApp\InAppCommunication\Http\Controllers\Messages\ListMessagesController;
use AidingApp\InAppCommunication\Http\Controllers\Users\SearchUsersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->name('in-app-communication.')
    ->prefix('api/chat')
    ->group(function () {
        Route::get('/users/search', SearchUsersController::class)
            ->name('users.search');

        Route::get('/conversations', ListConversationsController::class)
            ->name('conversations.index');

        Route::get('/conversations/public', ListPublicChannelsController::class)
            ->name('conversations.public');

        Route::post('/conversations', CreateConversationController::class)
            ->name('conversations.store');

        Route::get('/conversations/{conversation}', ShowConversationController::class)
            ->name('conversations.show');

        Route::patch('/conversations/{conversation}', UpdateConversationController::class)
            ->name('conversations.update');

        Route::post('/conversations/{conversation}/participants', AddParticipantController::class)
            ->name('conversations.participants.add');

        Route::patch('/conversations/{conversation}/participants/{user}', UpdateParticipantController::class)
            ->name('conversations.participants.update');

        Route::delete('/conversations/{conversation}/participants/{user}', RemoveParticipantController::class)
            ->name('conversations.participants.remove');

        Route::post('/conversations/{conversation}/leave', LeaveConversationController::class)
            ->name('conversations.leave');

        Route::post('/conversations/{conversation}/join', JoinChannelController::class)
            ->name('conversations.join');

        Route::patch('/conversations/{conversation}/settings', UpdateSettingsController::class)
            ->name('conversations.settings.update');

        Route::post('/conversations/{conversation}/read', MarkAsReadController::class)
            ->name('conversations.read');

        Route::get('/conversations/{conversation}/messages', ListMessagesController::class)
            ->name('conversations.messages.index');

        Route::post('/conversations/{conversation}/messages', CreateMessageController::class)
            ->name('conversations.messages.store');

        Route::post('/conversations/{conversation}/typing', BroadcastTypingController::class)
            ->name('conversations.typing');
    });
