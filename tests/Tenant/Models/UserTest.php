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

use App\Features\UserFullNameFeature;
use App\Models\Scopes\OrderByFullName;
use App\Models\Scopes\WhereFullNameLike;
use App\Models\User;
use App\Observers\UserObserver;

use function Pest\Laravel\assertDatabaseHas;

it('can always receive email', function () {
    $user = User::factory()->create();

    expect($user->canReceiveEmail())->toBeTrue();
});

describe('push subscriptions', function () {
    it('stores a push subscription for the user on the tenant connection', function () {
        $user = User::factory()->create();

        expect($user->pushSubscriptions()->count())->toBe(0);

        $subscription = $user->updatePushSubscription(
            endpoint: 'https://push.test/endpoint/abc',
            key: 'p256dh-key',
            token: 'auth-token',
        );

        expect($subscription->getConnectionName())->toBe('tenant')
            ->and($subscription->subscribable_id)->toBe($user->getKey())
            ->and($subscription->subscribable_type)->toBe($user->getMorphClass())
            ->and($user->pushSubscriptions()->count())->toBe(1);

        assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $user->getKey(),
            'subscribable_type' => $user->getMorphClass(),
            'endpoint' => 'https://push.test/endpoint/abc',
        ], 'tenant');
    });

    it('replaces an existing subscription for the same endpoint', function () {
        $user = User::factory()->create();

        $user->updatePushSubscription(endpoint: 'https://push.test/endpoint/abc', key: 'first');
        $user->updatePushSubscription(endpoint: 'https://push.test/endpoint/abc', key: 'second');

        expect($user->pushSubscriptions()->count())->toBe(1)
            ->and($user->pushSubscriptions()->first()->public_key)->toBe('second');
    });

    it('deletes a push subscription by endpoint', function () {
        $user = User::factory()->create();

        $user->updatePushSubscription(endpoint: 'https://push.test/endpoint/abc');

        expect($user->pushSubscriptions()->count())->toBe(1);

        $user->deletePushSubscription('https://push.test/endpoint/abc');

        expect($user->pushSubscriptions()->count())->toBe(0);
    });
});

describe('full name', function () {
    it('combines the first and last name', function () {
        $user = User::factory()->make(['first_name' => 'Jordan', 'last_name' => 'Blake']);

        expect($user->full_name)->toBe('Jordan Blake')
            ->and($user->getFilamentName())->toBe('Jordan Blake');
    });

    it('trims the full name when a part is blank', function () {
        $user = User::factory()->make(['first_name' => 'Jordan', 'last_name' => '']);

        expect($user->full_name)->toBe('Jordan');
    });

    it('orders users by first name then last name', function () {
        User::factory()->create(['first_name' => 'Zed', 'last_name' => 'Adams']);
        User::factory()->create(['first_name' => 'Amy', 'last_name' => 'Young']);
        User::factory()->create(['first_name' => 'Amy', 'last_name' => 'Baker']);

        expect(User::query()->tap(new OrderByFullName())->get()->pluck('full_name')->all())
            ->toBe(['Amy Baker', 'Amy Young', 'Zed Adams'])
            ->and(User::query()->tap(new OrderByFullName('desc'))->get()->pluck('full_name')->all())
            ->toBe(['Zed Adams', 'Amy Young', 'Amy Baker']);
    });

    it('searches users by their full name case-insensitively', function () {
        $matching = User::factory()->create(['first_name' => 'Jordan', 'last_name' => 'Blake']);
        User::factory()->create(['first_name' => 'Jordan', 'last_name' => 'Smith']);

        expect(User::query()->tap(new WhereFullNameLike('jordan BLA'))->pluck('id')->all())
            ->toBe([$matching->getKey()]);
    });

    it('keeps the legacy name column populated until the column has been dropped', function () {
        UserFullNameFeature::deactivate();

        $user = User::factory()->make(['first_name' => 'Jordan', 'last_name' => 'Blake']);

        app(UserObserver::class)->saving($user);

        expect($user->getAttributes())->toHaveKey('name', 'Jordan Blake');
    });

    it('does not write the legacy name column once it has been dropped', function () {
        $user = User::factory()->make(['first_name' => 'Jordan', 'last_name' => 'Blake']);

        app(UserObserver::class)->saving($user);

        expect($user->getAttributes())->not->toHaveKey('name');
    });
});
