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

use AidingApp\InAppCommunication\Actions\GetUserConversations;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Features\ConfidentialChannelsFeature;
use App\Models\User;

it('returns only confidential conversations when asked for them', function () {
    $user = User::factory()->create();

    $confidential = Conversation::factory()->confidential()->create();
    $ordinary = Conversation::factory()->channel()->create();

    ConversationParticipant::factory()->for($confidential)->for($user, 'participant')->create();
    ConversationParticipant::factory()->for($ordinary)->for($user, 'participant')->create();

    $results = app(GetUserConversations::class)(user: $user, confidential: true);

    expect($results->pluck('id')->all())
        ->toContain($confidential->getKey())
        ->not->toContain($ordinary->getKey());
});

it('excludes confidential conversations when asked to', function () {
    $user = User::factory()->create();

    $confidential = Conversation::factory()->confidential()->create();
    $ordinary = Conversation::factory()->channel()->create();

    ConversationParticipant::factory()->for($confidential)->for($user, 'participant')->create();
    ConversationParticipant::factory()->for($ordinary)->for($user, 'participant')->create();

    $results = app(GetUserConversations::class)(user: $user, confidential: false);

    expect($results->pluck('id')->all())
        ->toContain($ordinary->getKey())
        ->not->toContain($confidential->getKey());
});

it('returns both when no confidentiality filter is given', function () {
    $user = User::factory()->create();

    $confidential = Conversation::factory()->confidential()->create();
    $ordinary = Conversation::factory()->channel()->create();

    ConversationParticipant::factory()->for($confidential)->for($user, 'participant')->create();
    ConversationParticipant::factory()->for($ordinary)->for($user, 'participant')->create();

    $results = app(GetUserConversations::class)(user: $user);

    expect($results->pluck('id')->all())
        ->toContain($confidential->getKey())
        ->toContain($ordinary->getKey());
});

it('applies the confidentiality filter to pinned conversations', function () {
    $user = User::factory()->create();

    $confidential = Conversation::factory()->confidential()->create();
    $ordinary = Conversation::factory()->channel()->create();

    ConversationParticipant::factory()->pinned()->for($confidential)->for($user, 'participant')->create();
    ConversationParticipant::factory()->pinned()->for($ordinary)->for($user, 'participant')->create();

    $pinned = app(GetUserConversations::class)->pinned($user, null, true);

    expect($pinned->pluck('id')->all())
        ->toContain($confidential->getKey())
        ->not->toContain($ordinary->getKey());
});

it('ignores the confidentiality filter when the feature is inactive', function () {
    $user = User::factory()->create();

    $confidential = Conversation::factory()->confidential()->create();
    $ordinary = Conversation::factory()->channel()->create();

    ConversationParticipant::factory()->for($confidential)->for($user, 'participant')->create();
    ConversationParticipant::factory()->for($ordinary)->for($user, 'participant')->create();

    ConfidentialChannelsFeature::deactivate();

    $results = app(GetUserConversations::class)(user: $user, confidential: false);

    expect($results->pluck('id')->all())
        ->toContain($confidential->getKey())
        ->toContain($ordinary->getKey());
});
