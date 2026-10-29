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

use AidingApp\Engagement\Filament\Resources\EmailTemplates\EmailTemplateResource;
use AidingApp\Engagement\Filament\Resources\EmailTemplates\Pages\CreateEmailTemplate;
use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\Engagement\Tests\Tenant\Filament\Resources\EmailTemplates\RequestFactories\CreateEmailTemplateRequestFactory;
use App\Features\EmailTemplateNameUniquenessFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the create email template page', function (): void {
    asSuperAdmin();

    get(EmailTemplateResource::getUrl('create'))->assertSuccessful();
});

it('can create an email template', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.create']);
    actingAs($user);

    $request = CreateEmailTemplateRequestFactory::new()->create();

    assertDatabaseMissing(EmailTemplate::class, ['name' => $request['name']]);

    Livewire::test(CreateEmailTemplate::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas(EmailTemplate::class, [
        'name' => $request['name'],
        'description' => $request['description'],
        'user_id' => $user->getKey(),
    ]);
});

it('can create an email template with a soft deleted email template name', function (): void {
    asSuperAdmin();

    EmailTemplate::factory()->create(['name' => 'Welcome Email'])->delete();

    $request = CreateEmailTemplateRequestFactory::new()->state(['name' => 'Welcome Email'])->create();

    Livewire::test(CreateEmailTemplate::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(EmailTemplate::query()->where('name', 'Welcome Email')->count())->toBe(1);
});

it('validates the inputs', function (CreateEmailTemplateRequestFactory $data, array $errors): void {
    asSuperAdmin();

    EmailTemplate::factory()->create(['name' => 'Welcome Email']);

    Livewire::test(CreateEmailTemplate::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [CreateEmailTemplateRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [CreateEmailTemplateRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [CreateEmailTemplateRequestFactory::new()->state(['name' => 'Welcome Email']), ['name' => 'unique']],
    'name unique case insensitive' => [CreateEmailTemplateRequestFactory::new()->state(['name' => 'WELCOME EMAIL']), ['name' => 'unique']],
]);

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        EmailTemplateNameUniquenessFeature::deactivate();

        asSuperAdmin();

        EmailTemplate::factory()->create(['name' => 'Welcome Email']);

        $request = CreateEmailTemplateRequestFactory::new()->state(['name' => 'Welcome Email'])->create();

        expect(fn () => Livewire::test(CreateEmailTemplate::class)
            ->fillForm($request)
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the create page without the `settings.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        get(EmailTemplateResource::getUrl('create'))->assertForbidden();

        Livewire::test(CreateEmailTemplate::class)->assertForbidden();
    });

    it('allows access to the create page with the `settings.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.create']);
        actingAs($user);

        get(EmailTemplateResource::getUrl('create'))->assertSuccessful();

        Livewire::test(CreateEmailTemplate::class)->assertSuccessful();
    });
});
