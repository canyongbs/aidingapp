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

use AidingApp\Portal\Actions\GeneratePortalServiceRequestFormKitSchema;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use App\Features\StepDescriptionFeature;

/**
 * @param array<int|string, mixed> $schema
 *
 * @return array<int, array<string, mixed>>
 */
$findNodesByClass = function (array $schema, string $class) use (&$findNodesByClass): array {
    $nodes = [];

    foreach ($schema as $node) {
        if (! is_array($node)) {
            continue;
        }

        if (in_array($class, explode(' ', $node['attrs']['class'] ?? ''))) {
            $nodes[] = $node;
        }

        $nodes = [...$nodes, ...$findNodesByClass(is_array($node['children'] ?? null) ? $node['children'] : [], $class)];
    }

    return $nodes;
};

/**
 * @param array<int, array<string, mixed>> $nodes
 *
 * @return array<int, mixed>
 */
$boundText = fn (array $nodes): array => array_map(fn (array $node): mixed => $node['for'][1][0], $nodes);

it('renders the step title and description above each step of a multi-step form', function () use ($findNodesByClass, $boundText): void {
    $form = ServiceRequestForm::factory()->create(['is_wizard' => true]);

    $form->steps()->createMany([
        ['label' => 'Personal Information', 'description' => 'Tell us about yourself.', 'sort' => 0],
        ['label' => 'Review', 'description' => 'Check your answers.', 'sort' => 1],
    ]);

    $schema = app(GeneratePortalServiceRequestFormKitSchema::class)($form);

    expect($boundText($findNodesByClass([$schema], 'wizard-title')))->toBe(['Personal Information', 'Review'])
        ->and($boundText($findNodesByClass([$schema], 'wizard-description')))->toBe(['Tell us about yourself.', 'Check your answers.']);
});

it('omits the step description when the step does not have one', function () use ($findNodesByClass, $boundText): void {
    $form = ServiceRequestForm::factory()->create(['is_wizard' => true]);

    $form->steps()->createMany([
        ['label' => 'Personal Information', 'description' => 'Tell us about yourself.', 'sort' => 0],
        ['label' => 'Review', 'sort' => 1],
    ]);

    $schema = app(GeneratePortalServiceRequestFormKitSchema::class)($form);

    expect($findNodesByClass([$schema], 'wizard-header'))->toHaveCount(2)
        ->and($boundText($findNodesByClass([$schema], 'wizard-description')))->toBe(['Tell us about yourself.']);
});

it('binds the step title and description as data so FormKit renders them as literal text', function () use ($findNodesByClass, $boundText): void {
    $form = ServiceRequestForm::factory()->create(['is_wizard' => true]);

    $form->steps()->createMany([
        ['label' => 'Fees', 'description' => "\$100 maximum per item.\nUse \"quotes\" if needed.", 'sort' => 0],
        ['label' => 'Review', 'sort' => 1],
    ]);

    $schema = app(GeneratePortalServiceRequestFormKitSchema::class)($form);

    $titles = $findNodesByClass([$schema], 'wizard-title');
    $descriptions = $findNodesByClass([$schema], 'wizard-description');

    expect(array_column($titles, 'children'))->toBe(['$stepTitle', '$stepTitle'])
        ->and($boundText($titles))->toBe(['Fees', 'Review'])
        ->and(array_column($descriptions, 'children'))->toBe(['$stepDescription'])
        ->and($boundText($descriptions))->toBe(["\$100 maximum per item.\nUse \"quotes\" if needed."]);
});

it('omits the step description when the `StepDescriptionFeature` flag is inactive', function () use ($findNodesByClass): void {
    StepDescriptionFeature::deactivate();

    $form = ServiceRequestForm::factory()->create(['is_wizard' => true]);

    $form->steps()->createMany([
        ['label' => 'Personal Information', 'description' => 'Tell us about yourself.', 'sort' => 0],
        ['label' => 'Review', 'sort' => 1],
    ]);

    $schema = app(GeneratePortalServiceRequestFormKitSchema::class)($form);

    expect($findNodesByClass([$schema], 'wizard-title'))->toHaveCount(2)
        ->and($findNodesByClass([$schema], 'wizard-description'))->toBeEmpty();
});

it('renders the step progress and back and next navigation for a multi-step form', function () use ($findNodesByClass): void {
    $form = ServiceRequestForm::factory()->create(['is_wizard' => true]);

    $form->steps()->createMany([
        ['label' => 'Personal Information', 'sort' => 0],
        ['label' => 'Review', 'sort' => 1],
    ]);

    $schema = app(GeneratePortalServiceRequestFormKitSchema::class)($form);

    expect($findNodesByClass([$schema], 'step-progress')[0]['attrs']['style'])->toBeNull()
        ->and($findNodesByClass([$schema], 'step-nav__back')[0]['attrs']['style'])->toBeNull()
        ->and($findNodesByClass([$schema], 'step-progress__label')[0]['children'])->toBe('$: "Step " + $currentStep + " of " + $totalSteps')
        ->and($findNodesByClass([$schema], 'step-progress__percent')[0]['children'])->toBe('$percentComplete + "% complete"')
        ->and($findNodesByClass([$schema], 'step-nav__next-btn')[0]['children'][0]['onClick'])->toBe('$setStep(1)')
        ->and($findNodesByClass([$schema], 'wizard'))->toBeEmpty();
});

it('hides the step headers, progress, and back navigation for a single-step form', function () use ($findNodesByClass): void {
    $form = ServiceRequestForm::factory()->create(['is_wizard' => true]);

    $form->steps()->create(['label' => 'Main', 'description' => 'Tell us about yourself.', 'sort' => 0]);

    $schema = app(GeneratePortalServiceRequestFormKitSchema::class)($form);

    expect($findNodesByClass([$schema], 'wizard-header'))->toBeEmpty()
        ->and($findNodesByClass([$schema], 'step-progress')[0]['attrs']['style'])->toBe('display: none;')
        ->and($findNodesByClass([$schema], 'step-nav__back')[0]['attrs']['style'])->toBe('display: none;');
});
