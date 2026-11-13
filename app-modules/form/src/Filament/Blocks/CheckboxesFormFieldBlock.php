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

namespace AidingApp\Form\Filament\Blocks;

use AidingApp\Form\Filament\Forms\Components\OptionsKeyValue;
use AidingApp\Form\Models\SubmissibleField;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;

class CheckboxesFormFieldBlock extends FormFieldBlock
{
    public static function type(): string
    {
        return 'checkboxes';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return parent::configureEditorAction($action)
            ->modalWidth(Width::TwoExtraLarge);
    }

    /**
     * @return array<Component>
     */
    public static function fields(): array
    {
        return [
            Checkbox::make('hasOtherOption')
                ->label('Include Other')
                ->live(),
            OptionsKeyValue::make('options')
                ->required(fn (Get $get): bool => ! $get('hasOtherOption')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getFormKitSchema(SubmissibleField $field): array
    {
        if ($field->config['hasOtherOption'] ?? false) {
            return [
                '$formkit' => 'checkboxWithOther',
                'fieldLabel' => $field->label,
                'validationLabel' => $field->label,
                'name' => $field->id,
                ...($field->is_required ? ['validation' => 'required'] : []),
                'options' => $field->config['options'],
                ...(! empty($field->config['description']) ? ['description' => $field->config['description']] : []),
            ];
        }

        return [
            '$formkit' => 'checkbox',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
            'options' => $field->config['options'],
            ...self::getDescriptionSectionsSchema($field, 'legend'),
        ];
    }

    /**
     * @return array<int, string|Closure>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        if ($field->config['hasOtherOption'] ?? false) {
            return [
                'array',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (is_array($value) && collect($value)->contains(fn (mixed $selection): bool => ! is_string($selection))) {
                        $fail('The :attribute field must only contain text values.');
                    }
                },
            ];
        }

        /** @var array<array-key, mixed> $options */
        $options = $field->config['options'] ?? [];

        $values = array_is_list($options) ? array_column($options, 'value') : array_keys($options);

        return [
            'array',
            'in:' . implode(',', $values),
        ];
    }

    protected static function previewView(): string
    {
        return 'form::blocks.previews.checkboxes';
    }

    protected static function renderedView(): string
    {
        return 'form::blocks.submissions.checkboxes';
    }
}
