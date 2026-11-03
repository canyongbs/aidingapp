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
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\StepDescriptionFeature;

/**
 * Collects every schema node whose `class` attribute contains the given class name, so
 * assertions do not depend on the exact position of nodes within the schema tree.
 *
 * @param array<mixed> $schema
 *
 * @return array<int, array<string, mixed>>
 */
$findNodesByClass = function (array $schema, string $class) use (&$findNodesByClass): array {
    $nodes = [];

    if (in_array($class, explode(' ', (string) ($schema['attrs']['class'] ?? '')), true)) {
        $nodes[] = $schema;
    }

    foreach ($schema as $child) {
        if (is_array($child)) {
            $nodes = [...$nodes, ...$findNodesByClass($child, $class)];
        }
    }

    return $nodes;
};

$generateSchema = function (ServiceRequestType $type): array {
    $form = app(GenerateServiceRequestForm::class)->execute(
        $type,
        app(ResolveUploadsMediaCollectionForServiceRequest::class)(),
        shouldCombineFirstStepIfEnabled: true,
    );

    return app(GeneratePortalServiceRequestFormKitSchema::class)($form);
};

it('renders a title and description header for each step of a multi-step form', function () use ($findNodesByClass, $generateSchema): void {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_wizard' => true, 'is_first_step_combined' => false]);

    $form->steps()->create(['label' => 'Personal details', 'description' => 'Tell us about yourself.', 'sort' => 0]);
    $form->steps()->create(['label' => 'Review', 'sort' => 1]);

    $schema = $generateSchema($type);

    expect(array_column($findNodesByClass($schema, 'wizard-title'), 'children'))->toBe(['Main', 'Personal details', 'Review'])
        ->and(array_column($findNodesByClass($schema, 'wizard-description'), 'children'))->toBe(['Tell us about yourself.']);
});

it('titles the Main step after the step combined into it', function () use ($findNodesByClass, $generateSchema): void {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_wizard' => true, 'is_first_step_combined' => true]);

    $form->steps()->create(['label' => 'Personal details', 'description' => 'Tell us about yourself.', 'sort' => 0]);
    $form->steps()->create(['label' => 'Review', 'sort' => 1]);

    $schema = $generateSchema($type);

    expect(array_column($findNodesByClass($schema, 'wizard-title'), 'children'))->toBe(['Personal details', 'Review'])
        ->and(array_column($findNodesByClass($schema, 'wizard-description'), 'children'))->toBe(['Tell us about yourself.'])
        ->and(json_encode($schema, JSON_THROW_ON_ERROR))->toContain('"name":"Main"');
});

it('does not render step descriptions when `StepDescriptionFeature` is inactive', function () use ($findNodesByClass, $generateSchema): void {
    StepDescriptionFeature::deactivate();

    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_wizard' => true, 'is_first_step_combined' => false]);

    $form->steps()->create(['label' => 'Personal details', 'description' => 'Tell us about yourself.', 'sort' => 0]);

    $schema = $generateSchema($type);

    expect($findNodesByClass($schema, 'wizard-title'))->toHaveCount(2)
        ->and($findNodesByClass($schema, 'wizard-description'))->toBeEmpty();
});

it('renders back and next navigation around the step progress instead of step tabs', function () use ($findNodesByClass, $generateSchema): void {
    $type = ServiceRequestType::factory()->create();

    $form = ServiceRequestForm::factory()
        ->for($type, 'type')
        ->create(['is_wizard' => true, 'is_first_step_combined' => false]);

    $form->steps()->create(['label' => 'Personal details', 'sort' => 0]);

    $schema = $generateSchema($type);
    $encodedSchema = json_encode($schema, JSON_THROW_ON_ERROR);

    $back = $findNodesByClass($schema, 'step-nav__back')[0];
    $progress = $findNodesByClass($schema, 'step-progress')[0];
    $next = $findNodesByClass($schema, 'step-nav__next-btn')[0];

    expect($back['attrs']['style'])->toBeNull()
        ->and($back['children'][0])->toMatchArray(['disabled' => '$currentStep === 1', 'onClick' => '$setStep(-1)', 'children' => 'Back'])
        ->and($progress['attrs']['style'])->toBeNull()
        ->and(array_column($progress['children'], 'children'))->toBe(['$: "Step " + $currentStep + " of " + $totalSteps', '$percentComplete + "% complete"'])
        ->and($next['attrs']['style']['if'])->toBe('$currentStep === $totalSteps')
        ->and($next['children'][0])->toMatchArray(['onClick' => '$setStep(1)', 'children' => 'Next'])
        ->and($encodedSchema)->toContain('"$formkit":"submit"')
        ->and($findNodesByClass($schema, 'wizard'))->toBeEmpty()
        ->and($encodedSchema)->not->toContain('$setActiveStep');
});

it('hides the step header, back button and progress for a single-step form', function () use ($findNodesByClass, $generateSchema): void {
    $type = ServiceRequestType::factory()->create();

    $schema = $generateSchema($type);

    expect($findNodesByClass($schema, 'wizard-header'))->toBeEmpty()
        ->and($findNodesByClass($schema, 'step-nav__back')[0]['attrs']['style'])->toBe('display: none;')
        ->and($findNodesByClass($schema, 'step-progress')[0]['attrs']['style'])->toBe('display: none;');
});
