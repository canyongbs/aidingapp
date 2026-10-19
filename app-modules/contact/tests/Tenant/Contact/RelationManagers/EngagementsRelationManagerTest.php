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

use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\EngagementsRelationManager;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use AidingApp\Timeline\Listeners\AddRecordToTimeline;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

/**
 * Seeds the given contact with populated outbound (Engagement) and inbound
 * (EngagementResponse) email records, each wired into the contact's timeline.
 *
 * @return array{outbound: Collection<int, Engagement>, inbound: Collection<int, EngagementResponse>}
 */
$seedContactEmails = function (Contact $contact): array {
    $outbound = Engagement::factory()->count(2)
        ->state([
            'recipient_id' => $contact->getKey(),
            'recipient_type' => $contact->getMorphClass(),
        ])->createQuietly();
    $inbound = EngagementResponse::factory()->count(3)
        ->state([
            'sender_id' => $contact->getKey(),
            'sender_type' => $contact->getMorphClass(),
        ])->createQuietly();
    $outbound->each(function (Engagement $engagement): void {
        assert($engagement->recipient instanceof Model);

        app(AddRecordToTimeline::class)->handle(
            new TimelineableRecordCreated($engagement->recipient, $engagement),
        );
    });
    $inbound->each(function (EngagementResponse $response): void {
        app(AddRecordToTimeline::class)->handle(
            new TimelineableRecordCreated($response->sender, $response),
        );
    });

    return ['outbound' => $outbound, 'inbound' => $inbound];
};

it('disables the `engage` header action with the bounced tooltip for a bounced owner contact', function () {
    asSuperAdmin();

    $contact = Contact::factory()->bounced()->create();

    $component = Livewire::test(EngagementsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->assertActionDisabled(TestAction::make('engage')->table());

    $action = $component->instance()->getTable()->getAction('engage');

    expect($action)->not()->toBeNull()
        ->and($action->getTooltip())->toBe('Bounced. Email delivery failed and a bounce was received from our email provider.');
});

it('enables the `engage` header action with no tooltip for a healthy owner contact', function () {
    asSuperAdmin();

    $contact = Contact::factory()->create(['email_bounce' => false]);

    $component = Livewire::test(EngagementsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->assertActionEnabled(TestAction::make('engage')->table());

    $action = $component->instance()->getTable()->getAction('engage');

    expect($action)->not()->toBeNull()
        ->and($action->getTooltip())->toBeNull();
});

describe('email direction filtering by permission', function () use ($seedContactEmails) {
    it('shows only outbound emails to a user with only the `engagement.view-any` permission', function () use ($seedContactEmails) {
        $user = User::factory()->create();
        $user->givePermissionTo('engagement.view-any');

        actingAs($user);

        $contact = Contact::factory()->create();

        $emails = $seedContactEmails($contact);

        Livewire::test(EngagementsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords($emails['outbound']->pluck('timelineRecord'))
            ->assertCanNotSeeTableRecords($emails['inbound']->pluck('timelineRecord'));
    });

    it('shows only inbound emails to a user with only the `engagement_response.view-any` permission', function () use ($seedContactEmails) {
        $user = User::factory()->create();
        $user->givePermissionTo('engagement_response.view-any');

        actingAs($user);

        $contact = Contact::factory()->create();

        $emails = $seedContactEmails($contact);

        Livewire::test(EngagementsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords($emails['inbound']->pluck('timelineRecord'))
            ->assertCanNotSeeTableRecords($emails['outbound']->pluck('timelineRecord'));
    });

    it('shows both directions to a user with both engagement permissions', function () use ($seedContactEmails) {
        $user = User::factory()->create();
        $user->givePermissionTo('engagement.view-any');
        $user->givePermissionTo('engagement_response.view-any');

        actingAs($user);

        $contact = Contact::factory()->create();

        $emails = $seedContactEmails($contact);

        Livewire::test(EngagementsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords(
                $emails['outbound']->pluck('timelineRecord')->merge($emails['inbound']->pluck('timelineRecord')),
            );
    });
});

describe('authorization', function () {
    it('does not show the Emails tab to a user with neither engagement permission', function () {
        $user = User::factory()->create();

        actingAs($user);

        $contact = Contact::factory()->create();

        expect(EngagementsRelationManager::canViewForRecord($contact, ViewContact::class))->toBeFalse();
    });

    it('shows the Emails tab to a user with only the `engagement.view-any` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('engagement.view-any');

        actingAs($user);

        $contact = Contact::factory()->create();

        expect(EngagementsRelationManager::canViewForRecord($contact, ViewContact::class))->toBeTrue();
    });

    it('shows the Emails tab to a user with only the `engagement_response.view-any` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('engagement_response.view-any');

        actingAs($user);

        $contact = Contact::factory()->create();

        expect(EngagementsRelationManager::canViewForRecord($contact, ViewContact::class))->toBeTrue();
    });
});
