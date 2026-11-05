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
use AidingApp\Engagement\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\Engagement\Tests\Tenant\Filament\Resources\EmailTemplates\RequestFactories\EditEmailTemplateRequestFactory;
use App\Features\EmailTemplateNameUniquenessFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the edit email template page', function (): void {
    asSuperAdmin();
    $emailTemplate = EmailTemplate::factory()->create();

    get(EmailTemplateResource::getUrl('edit', ['record' => $emailTemplate]))->assertSuccessful();
});

it('displays the email template data', function (): void {
    asSuperAdmin();
    $emailTemplate = EmailTemplate::factory()->create();

    Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $emailTemplate->name,
            'description' => $emailTemplate->description,
        ]);
});

it('can update an email template', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);
    actingAs($user);

    $emailTemplate = EmailTemplate::factory()->create();

    $request = EditEmailTemplateRequestFactory::new()->create();

    Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(EmailTemplate::class, [
        'id' => $emailTemplate->getKey(),
        'name' => $request['name'],
        'description' => $request['description'],
    ]);
});

it('allows saving with the same name as the current email template', function (): void {
    asSuperAdmin();
    $emailTemplate = EmailTemplate::factory()->create(['name' => 'Welcome Email']);

    Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])
        ->fillForm(['name' => 'WELCOME EMAIL'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($emailTemplate->refresh()->name)->toBe('WELCOME EMAIL');
});

it('allows saving with the name of a soft deleted email template', function (): void {
    asSuperAdmin();

    EmailTemplate::factory()->create(['name' => 'Welcome Email'])->delete();
    $emailTemplate = EmailTemplate::factory()->create(['name' => 'Reminder Email']);

    Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])
        ->fillForm(['name' => 'Welcome Email'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($emailTemplate->refresh()->name)->toBe('Welcome Email');
});

it('validates the inputs', function (EditEmailTemplateRequestFactory $data, array $errors): void {
    asSuperAdmin();

    EmailTemplate::factory()->create(['name' => 'Welcome Email']);
    $emailTemplate = EmailTemplate::factory()->create();

    Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [EditEmailTemplateRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [EditEmailTemplateRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [EditEmailTemplateRequestFactory::new()->state(['name' => 'Welcome Email']), ['name' => 'unique']],
    'name unique case insensitive' => [EditEmailTemplateRequestFactory::new()->state(['name' => 'WELCOME EMAIL']), ['name' => 'unique']],
]);

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        EmailTemplateNameUniquenessFeature::deactivate();

        asSuperAdmin();

        EmailTemplate::factory()->create(['name' => 'Welcome Email']);
        $emailTemplate = EmailTemplate::factory()->create(['name' => 'Reminder Email']);

        expect(fn () => Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])
            ->fillForm(['name' => 'Welcome Email'])
            ->call('save'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the edit page without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $emailTemplate = EmailTemplate::factory()->create();

        get(EmailTemplateResource::getUrl('edit', ['record' => $emailTemplate]))->assertForbidden();

        Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])->assertForbidden();
    });

    it('allows access to the edit page with the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.update']);
        actingAs($user);

        $emailTemplate = EmailTemplate::factory()->create();

        get(EmailTemplateResource::getUrl('edit', ['record' => $emailTemplate]))->assertSuccessful();

        Livewire::test(EditEmailTemplate::class, ['record' => $emailTemplate->getRouteKey()])->assertSuccessful();
    });
});
