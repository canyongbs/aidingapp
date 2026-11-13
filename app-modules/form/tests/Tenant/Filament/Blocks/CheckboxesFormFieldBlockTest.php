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

use AidingApp\Form\Filament\Blocks\CheckboxesFormFieldBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\Livewire;

class CheckboxesFormFieldBlockTestHost extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    /** @var array<string, mixed> */
    public array $data = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(CheckboxesFormFieldBlock::fields())
            ->statePath('data');
    }

    public function save(): void
    {
        $this->getSchema('form')?->getState();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}

$options = [
    ['label' => 'Red', 'value' => 'red'],
    ['label' => 'Blue', 'value' => 'blue'],
];

it('requires at least one option unless the other option is included', function (bool $hasOtherOption, array $options, bool $hasErrors): void {
    $test = Livewire::test(CheckboxesFormFieldBlockTestHost::class)
        ->set('data.hasOtherOption', $hasOtherOption)
        ->set('data.options', $options)
        ->call('save');

    $hasErrors ? $test->assertHasErrors(['data.options' => 'required']) : $test->assertHasNoErrors();
})->with([
    'no options without other' => [false, [], true],
    'no options with other' => [true, [], false],
    'an option without other' => [false, [['key' => 'red', 'value' => 'Red']], false],
]);

it('renders every option label in the preview', function () use ($options): void {
    $html = CheckboxesFormFieldBlock::toPreviewHtml([
        'label' => 'Colors',
        'isRequired' => false,
        'options' => $options,
        'hasOtherOption' => true,
    ]);

    expect($html)->toContain('Red')->toContain('Blue')->toContain('Other');
});

it('builds the FormKit schema with or without the other option', function (bool $hasOtherOption, string $formkit) use ($options): void {
    $field = new ServiceRequestFormField([
        'label' => 'Colors',
        'is_required' => true,
        'config' => ['hasOtherOption' => $hasOtherOption, 'options' => $options],
    ]);

    expect(CheckboxesFormFieldBlock::getFormKitSchema($field)['$formkit'])->toBe($formkit);
})->with([
    'with other' => [true, 'checkboxWithOther'],
    'without other' => [false, 'checkbox'],
]);

it('only accepts configured values unless the other option is included', function () use ($options): void {
    $makeField = fn (bool $hasOtherOption): ServiceRequestFormField => new ServiceRequestFormField([
        'label' => 'Colors',
        'config' => ['hasOtherOption' => $hasOtherOption, 'options' => $options],
    ]);

    $validate = fn (bool $hasOtherOption): bool => Validator::make(
        ['response' => ['green']],
        ['response' => CheckboxesFormFieldBlock::getValidationRules($makeField($hasOtherOption))],
    )->passes();

    expect($validate(false))->toBeFalse()
        ->and($validate(true))->toBeTrue();
});

it('rejects non-string selections even when the other option is included', function (mixed $response, bool $passes) use ($options): void {
    $field = new ServiceRequestFormField([
        'label' => 'Colors',
        'config' => ['hasOtherOption' => true, 'options' => $options],
    ]);

    expect(Validator::make(
        ['response' => $response],
        ['response' => CheckboxesFormFieldBlock::getValidationRules($field)],
    )->passes())->toBe($passes);
})->with([
    'nested array' => [[['Teal']], false],
    'integer' => [[1], false],
    'free text' => [['red', 'Teal'], true],
]);

it('shows which options were selected in the submission view', function () use ($options): void {
    $html = view('form::blocks.submissions.checkboxes', [
        'label' => 'Colors',
        'isRequired' => false,
        'options' => $options,
        'hasOtherOption' => true,
        'response' => json_encode(['red', 'Teal']),
    ])->render();

    expect($html)->toContain('Red')->toContain('Blue')->toContain('Other: Teal')
        ->and($html)->not->toContain('No response');
});
