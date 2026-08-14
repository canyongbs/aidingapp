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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestConversationsRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestConversation;
use App\Settings\LicenseSettings;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('lists the finished conversations for the service request', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $finished = ServiceRequestConversation::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->finished()
        ->create();

    livewire(ServiceRequestConversationsRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ViewServiceRequest::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$finished]);
});

it('does not list conversations that have not finished', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $unfinished = ServiceRequestConversation::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    livewire(ServiceRequestConversationsRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ViewServiceRequest::class,
    ])
        ->assertSuccessful()
        ->assertCanNotSeeTableRecords([$unfinished]);
});

it('does not list conversations belonging to another service request', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $otherConversation = ServiceRequestConversation::factory()
        ->for(ServiceRequest::factory(), 'serviceRequest')
        ->finished()
        ->create();

    livewire(ServiceRequestConversationsRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ViewServiceRequest::class,
    ])
        ->assertSuccessful()
        ->assertCanNotSeeTableRecords([$otherConversation]);
});

it('sorts the most recently finished conversation first', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $older = ServiceRequestConversation::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->finished()
        ->create(['finished_at' => now()->subDays(2)]);

    $newer = ServiceRequestConversation::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->finished()
        ->create(['finished_at' => now()->subDay()]);

    livewire(ServiceRequestConversationsRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ViewServiceRequest::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
});

it('links each conversation to its transcript', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $conversation = ServiceRequestConversation::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->finished()
        ->create();

    livewire(ServiceRequestConversationsRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ViewServiceRequest::class,
    ])
        ->assertSuccessful()
        ->assertSeeHtml(ServiceRequestResource::getUrl('view-live-chat-transcript', [
            'record' => $serviceRequest,
            'conversation' => $conversation,
        ]));
});

describe('authorization', function () {
    it('is not viewable when the `RealtimeChat` feature is disabled', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        expect(ServiceRequestConversationsRelationManager::canViewForRecord($serviceRequest, ViewServiceRequest::class))
            ->toBeTrue();

        $settings = app(LicenseSettings::class);
        $settings->data->addons->realtimeChat = false;
        $settings->save();

        expect(ServiceRequestConversationsRelationManager::canViewForRecord($serviceRequest, ViewServiceRequest::class))
            ->toBeFalse();
    });
});
