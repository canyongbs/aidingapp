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

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\Timeline\Livewire\TimelineList;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

it('renders the timeline records for the given record', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create(['update' => 'A distinctive timeline entry']);
    $renderedTimeline = Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])->assertSuccessful();
    expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain($update->update);
});

it('shows the empty state message when there is nothing to timeline', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();
    $renderedTimeline = Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
        'emptyStateMessage' => 'Nothing has happened yet.',
    ])->assertSuccessful();
    expect(\Tests\htmlText($renderedTimeline->html(), '//div[contains(@class, "border-dashed")]/span'))->toContain('Nothing has happened yet.');
});

it('loads one page of records at a time and flags that more remain', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->count(7)
        ->create();

    $component = Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])->assertSuccessful();

    $instance = $component->instance();

    assert($instance instanceof TimelineList);

    expect($instance->timelineRecords)->toHaveCount(5)
        ->and($instance->hasMorePages)->toBeTrue();

    $component->call('loadTimelineRecords')
        ->assertSuccessful();
});

it('refuses to open a record from another entity timeline', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();
    $otherServiceRequest = ServiceRequest::factory()->create();

    $otherUpdate = ServiceRequestUpdate::factory()
        ->for($otherServiceRequest, 'serviceRequest')
        ->create();

    Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('viewRecord', $otherUpdate->getKey(), $otherUpdate->getMorphClass())
        ->assertStatus(404);
});

it('refuses to open a record of a type this timeline does not display', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestAssignment::class],
    ])
        ->call('viewRecord', $update->getKey(), $update->getMorphClass())
        ->assertStatus(404);
});

it('returns a 404 when the view action is mounted without a record', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('mountAction', 'view')
        ->assertStatus(404);
});

it('keeps the opened record selected when more timeline records are loaded', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('viewRecord', $update->getKey(), $update->getMorphClass())
        ->assertSuccessful()
        ->call('loadTimelineRecords')
        ->assertSuccessful()
        ->assertSet('currentRecordToView.id', $update->getKey());
});

it('opens the slide over for a timeline record', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ])
        ->call('viewRecord', $update->getKey(), $update->getMorphClass())
        ->assertSuccessful()
        ->assertSet('currentRecordToView.id', $update->getKey());
});

it('opens the slide over for a soft-deleted timeline record', function (): void {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create();

    $update = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    $component = Livewire::test(TimelineList::class, [
        'record' => $serviceRequest,
        'modelsToTimeline' => [ServiceRequestUpdate::class],
    ]);

    // A query-builder delete skips the observer, leaving the timeline row in place.
    ServiceRequestUpdate::query()->whereKey($update->getKey())->delete();

    expect($update->refresh()->trashed())->toBeTrue();

    $component
        ->call('viewRecord', $update->getKey(), $update->getMorphClass())
        ->assertSuccessful()
        ->assertSet('currentRecordToView.id', $update->getKey());
});

describe('related people', function (): void {
    it('shows a soft-deleted person by name', function (): void {
        asSuperAdmin();

        $performer = User::factory()->create(['name' => 'Jane Doe']);

        $checkOut = AssetCheckOut::factory()->create([
            'checked_out_by_type' => $performer->getMorphClass(),
            'checked_out_by_id' => $performer->getKey(),
        ]);

        $performer->delete();

        expect($performer->refresh()->trashed())->toBeTrue();
        $renderedTimeline = Livewire::test(TimelineList::class, [
            'record' => $checkOut->asset,
            'modelsToTimeline' => [AssetCheckOut::class],
        ])->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Jane Doe');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->not()->toContain('Deleted user');
    });

    it('shows `Deleted user` when the person no longer exists', function (): void {
        asSuperAdmin();

        $checkOut = AssetCheckOut::factory()->create([
            'checked_out_by_type' => (new User())->getMorphClass(),
            'checked_out_by_id' => Str::uuid()->toString(),
        ]);
        $renderedTimeline = Livewire::test(TimelineList::class, [
            'record' => $checkOut->asset,
            'modelsToTimeline' => [AssetCheckOut::class],
        ])->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Deleted user');
    });

    it('shows the soft-deleted creator of an engagement by name', function (): void {
        asSuperAdmin();

        $creator = User::factory()->create(['name' => 'Jane Doe']);
        $contact = Contact::factory()->create();

        Engagement::factory()
            ->for($creator, 'user')
            ->for($contact, 'recipient')
            ->create();

        $creator->delete();

        expect($creator->refresh()->trashed())->toBeTrue();
        $renderedTimeline = Livewire::test(TimelineList::class, [
            'record' => $contact,
            'modelsToTimeline' => [Engagement::class],
        ])->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Jane Doe');
    });
});
