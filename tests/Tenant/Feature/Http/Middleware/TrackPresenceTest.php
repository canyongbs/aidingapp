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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use App\Http\Middleware\TrackPresence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;

it('updates last_activity_at on a standard page request', function () {
    $user = User::factory()->create(['last_activity_at' => null]);

    actingAs($user);

    $request = Request::create('/admin', 'GET');
    $request->setUserResolver(fn () => $user);

    $middleware = new TrackPresence();
    $middleware->handle($request, fn () => new Response());

    $user->refresh();

    expect($user->last_activity_at)->not->toBeNull();
});

it('does not update last_activity_at for livewire requests with no calls or updates', function () {
    $user = User::factory()->create(['last_activity_at' => null]);

    actingAs($user);

    $request = Request::create('/livewire/update', 'POST', [
        'components' => [
            [
                'snapshot' => '{}',
                'updates' => [],
                'calls' => [],
            ],
        ],
    ]);
    $request->headers->set('X-Livewire', 'true');
    $request->setUserResolver(fn () => $user);

    $middleware = new TrackPresence();
    $middleware->handle($request, fn () => new Response());

    $user->refresh();

    expect($user->last_activity_at)->toBeNull();
});

it('updates last_activity_at for livewire requests with calls', function () {
    $user = User::factory()->create(['last_activity_at' => null]);

    actingAs($user);

    $request = Request::create('/livewire/update', 'POST', [
        'components' => [
            [
                'snapshot' => '{}',
                'updates' => [],
                'calls' => [
                    ['method' => 'save', 'params' => []],
                ],
            ],
        ],
    ]);
    $request->headers->set('X-Livewire', 'true');
    $request->setUserResolver(fn () => $user);

    $middleware = new TrackPresence();
    $middleware->handle($request, fn () => new Response());

    $user->refresh();

    expect($user->last_activity_at)->not->toBeNull();
});

it('updates last_activity_at for livewire requests with updates', function () {
    $user = User::factory()->create(['last_activity_at' => null]);

    actingAs($user);

    $request = Request::create('/livewire/update', 'POST', [
        'components' => [
            [
                'snapshot' => '{}',
                'updates' => ['name' => 'John'],
                'calls' => [],
            ],
        ],
    ]);
    $request->headers->set('X-Livewire', 'true');
    $request->setUserResolver(fn () => $user);

    $middleware = new TrackPresence();
    $middleware->handle($request, fn () => new Response());

    $user->refresh();

    expect($user->last_activity_at)->not->toBeNull();
});

it('does not update last_activity_at for livewire requests that just update chart data', function () {
    $user = User::factory()->create(['last_activity_at' => null]);

    actingAs($user);

    $request = Request::create('/livewire/update', 'POST', [
        'components' => [
            [
                'snapshot' => '{}',
                'updates' => [],
                'calls' => [
                    [
                        'path' => '',
                        'method' => 'updateChartData',
                        'params' => [],
                    ],
                ],
            ],
            [
                'snapshot' => '{}',
                'updates' => [],
                'calls' => [
                    [
                        'path' => '',
                        'method' => 'updateChartData',
                        'params' => [],
                    ],
                ],
            ],
        ],
    ]);
    $request->headers->set('X-Livewire', 'true');
    $request->setUserResolver(fn () => $user);

    $middleware = new TrackPresence();
    $middleware->handle($request, fn () => new Response());

    $user->refresh();

    expect($user->last_activity_at)->toBeNull();
});

it('updates last_activity_at for livewire requests with calls batched with updates to chart data', function () {
    $user = User::factory()->create(['last_activity_at' => null]);

    actingAs($user);

    $request = Request::create('/livewire/update', 'POST', [
        'components' => [
            [
                'snapshot' => '{}',
                'updates' => [],
                'calls' => [
                    [
                        'path' => '',
                        'method' => 'updateChartData',
                        'params' => [],
                    ],
                    ['method' => 'save', 'params' => []],
                ],
            ],
        ],
    ]);
    $request->headers->set('X-Livewire', 'true');
    $request->setUserResolver(fn () => $user);

    $middleware = new TrackPresence();
    $middleware->handle($request, fn () => new Response());

    $user->refresh();

    expect($user->last_activity_at)->not->toBeNull();
});
