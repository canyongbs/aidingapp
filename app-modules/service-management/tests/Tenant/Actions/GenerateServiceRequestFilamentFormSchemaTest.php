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

use AidingApp\ServiceManagement\Actions\GenerateServiceRequestFilamentFormSchema;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Illuminate\Database\Eloquent\Collection;

it('resolves stored options into value and label maps for select and radio fields', function (string $type, array $options): void {
    $field = new ServiceRequestFormField([
        'type' => $type,
        'label' => 'Choice',
        'is_required' => false,
        'config' => ['options' => $options],
    ]);
    $field->id = (string) str()->uuid();

    $form = new ServiceRequestForm([
        'is_wizard' => false,
        'content' => [
            'content' => [
                ['type' => 'customBlock', 'attrs' => ['id' => $type, 'config' => ['fieldId' => $field->id]]],
            ],
        ],
    ]);
    $form->setRelation('fields', new Collection([$field]));

    $components = (new GenerateServiceRequestFilamentFormSchema())($form);

    expect($components)->toHaveCount(1);

    $component = $components[0];

    assert($component instanceof Select || $component instanceof Radio);

    expect($component->getOptions())->toBe(['0' => 'No', '1' => 'Yes']);
})->with([
    'select with a legacy map' => ['select', [0 => 'No', 1 => 'Yes']],
    'select with label and value rows' => ['select', [['label' => 'No', 'value' => '0'], ['label' => 'Yes', 'value' => '1']]],
    'radio with label and value rows' => ['radio', [['label' => 'No', 'value' => '0'], ['label' => 'Yes', 'value' => '1']]],
]);

it('adds an other option and a free-text input when the field allows other answers', function (string $type, string $componentClass): void {
    $field = new ServiceRequestFormField([
        'type' => $type,
        'label' => 'Choice',
        'is_required' => false,
        'config' => ['hasOtherOption' => true, 'options' => [['label' => 'No', 'value' => '0'], ['label' => 'Yes', 'value' => '1']]],
    ]);
    $field->id = (string) str()->uuid();

    $form = new ServiceRequestForm([
        'is_wizard' => false,
        'content' => [
            'content' => [
                ['type' => 'customBlock', 'attrs' => ['id' => $type, 'config' => ['fieldId' => $field->id]]],
            ],
        ],
    ]);
    $form->setRelation('fields', new Collection([$field]));

    $components = (new GenerateServiceRequestFilamentFormSchema())($form);

    expect($components)->toHaveCount(1)->and($components[0])->toBeInstanceOf(Group::class);

    [$choice, $other] = $components[0]->getDefaultChildComponents();

    expect($choice)->toBeInstanceOf($componentClass)
        ->and($choice->getOptions())->toBe([0 => 'No', 1 => 'Yes', '__other__' => 'Other'])
        ->and($other)->toBeInstanceOf(TextInput::class)
        ->and($other->getName())->toBe($field->id . '_other');
})->with([
    'radio' => ['radio', Radio::class],
    'checkboxes' => ['checkboxes', CheckboxList::class],
]);
