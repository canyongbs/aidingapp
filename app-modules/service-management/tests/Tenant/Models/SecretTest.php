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

use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Support\Collection;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;

it('hides its value from serialization', function () {
    $secret = Secret::factory()->create();

    expect($secret->toArray())->not->toHaveKey('value');
});

it('prunes only unattached secrets older than one day', function () {
    $expiredSecret = Secret::factory()->create([
        'updated_at' => now()->subDays(2),
    ]);
    $recentSecret = Secret::factory()->create();
    $attachedSecret = Secret::factory()
        ->for(ServiceRequest::factory(), 'related')
        ->create([
            'updated_at' => now()->subDays(2),
        ]);

    artisan(PruneCommand::class, [
        '--model' => Secret::class,
    ])->assertSuccessful();

    assertModelMissing($expiredSecret);
    assertModelExists($recentSecret);
    assertModelExists($attachedSecret);
});

it('is scheduled for daily pruning', function () {
    $schedule = app()->make(Schedule::class);

    $events = (new Collection($schedule->events()))->filter(function (Event $event) {
        $secretClass = Secret::class;

        return str_contains($event->command, "model:prune --model={$secretClass}")
            && $event->expression === '0 0 * * *';
    });

    expect($events)->toHaveCount(1);
});
