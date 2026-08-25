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

use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

function serviceRequestFormStepContent(string $label): array
{
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'customBlock',
                'attrs' => [
                    'id' => TextInputFormFieldBlock::type(),
                    'config' => [
                        'label' => $label,
                        'isRequired' => true,
                    ],
                ],
            ],
        ],
    ];
}

it('keeps the default fields and custom content as separate steps by default', function () {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_first_step_combined' => false]);

    $form->steps()->create([
        'label' => 'Details',
        'sort' => 0,
        'content' => serviceRequestFormStepContent('Preferred name'),
    ]);

    $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

    $result = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

    expect($result->steps)->toHaveCount(2)
        ->and($result->steps->first()->label)->toBe('Main')
        ->and($result->steps->first()->content['content'])->toHaveCount(4)
        ->and($result->steps->last()->label)->toBe('Details');
});

it('does not combine the first step when the caller does not opt in, even if the flag is set', function () {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_first_step_combined' => true]);

    $form->steps()->create([
        'label' => 'Details',
        'sort' => 0,
        'content' => serviceRequestFormStepContent('Preferred name'),
    ]);

    $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

    $result = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection, shouldCombineFirstStepIfEnabled: false);

    expect($result->steps)->toHaveCount(2)
        ->and($result->steps->first()->label)->toBe('Main')
        ->and($result->steps->first()->content['content'])->toHaveCount(4)
        ->and($result->steps->last()->label)->toBe('Details');
});

it('does not combine the first step when the form has not opted in, even if the caller allows it', function () {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_first_step_combined' => false]);

    $form->steps()->create([
        'label' => 'Details',
        'sort' => 0,
        'content' => serviceRequestFormStepContent('Preferred name'),
    ]);

    $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

    $result = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection, shouldCombineFirstStepIfEnabled: true);

    expect($result->steps)->toHaveCount(2)
        ->and($result->steps->first()->label)->toBe('Main')
        ->and($result->steps->first()->content['content'])->toHaveCount(4)
        ->and($result->steps->last()->label)->toBe('Details');
});

it('combines the single custom step into the Main step when both the caller and form opt in', function () {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create([
            'is_first_step_combined' => true,
            'content' => serviceRequestFormStepContent('Preferred name'),
        ]);

    $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

    $result = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection, shouldCombineFirstStepIfEnabled: true);

    expect($result->steps)->toHaveCount(1)
        ->and($result->steps->first()->label)->toBe('Main');

    $mainContent = $result->steps->first()->content['content'];

    expect($mainContent)->toHaveCount(5)
        ->and($mainContent[4]['attrs']['config']['label'])->toBe('Preferred name');
});

it('combines only the first step of a multi-step custom form into Main, keeping remaining steps separate', function () {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_first_step_combined' => true]);

    $form->steps()->create([
        'label' => 'Step One',
        'sort' => 0,
        'content' => serviceRequestFormStepContent('Preferred name'),
    ]);

    $form->steps()->create([
        'label' => 'Step Two',
        'sort' => 1,
        'content' => serviceRequestFormStepContent('Emergency contact'),
    ]);

    $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

    $result = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection, shouldCombineFirstStepIfEnabled: true);

    expect($result->steps)->toHaveCount(2)
        ->and($result->steps->first()->label)->toBe('Main')
        ->and($result->steps->first()->content['content'])->toHaveCount(5)
        ->and($result->steps->last()->label)->toBe('Step Two');
});
