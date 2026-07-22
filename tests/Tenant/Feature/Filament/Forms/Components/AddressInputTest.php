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
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\CreateContact;
use App\Models\User;
use App\Services\AwsGeoPlacesService;
use Illuminate\Support\Facades\Exceptions;
use Mockery\MockInterface;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('does not query the address suggestion service for searches shorter than three characters', function () {
    actingAs(
        User::factory()
            ->create()
            ->givePermissionTo('contact.view-any', 'contact.create')
    );

    /** @phpstan-ignore method.notFound */
    $this->mock(AwsGeoPlacesService::class, function (MockInterface $mock) {
        $mock->shouldNotReceive('autocompleteComponents');
    });

    livewire(CreateContact::class)
        ->call('callSchemaComponentMethod', 'form.address', 'getSearchResultsForJs', ['ab'])
        ->assertReturned([]);
});

it('returns no suggestions and notifies the user when the address suggestion service fails', function () {
    Exceptions::fake();

    actingAs(
        User::factory()
            ->create()
            ->givePermissionTo('contact.view-any', 'contact.create')
    );

    /** @phpstan-ignore method.notFound */
    $this->mock(AwsGeoPlacesService::class, function (MockInterface $mock) {
        $mock->shouldReceive('autocompleteComponents')
            /** @phpstan-ignore method.notFound */
            ->once()
            ->andThrow(new Exception('AWS GeoPlaces is unavailable'));
    });

    livewire(CreateContact::class)
        ->call('callSchemaComponentMethod', 'form.address', 'getSearchResultsForJs', ['123 Main'])
        ->assertNotified('Failed to fetch address suggestions')
        ->assertReturned([]);

    Exceptions::assertReported(fn (Exception $exception): bool => $exception->getMessage() === 'AWS GeoPlaces is unavailable');
});

it('limits the address to 255 characters', function () {
    actingAs(
        User::factory()
            ->create()
            ->givePermissionTo('contact.view-any', 'contact.create')
    );

    livewire(CreateContact::class)
        ->fillForm([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'full_name' => 'Jane Doe',
            'address' => str_repeat('a', 256),
        ])
        ->call('create')
        ->assertHasFormErrors(['address']);
});
