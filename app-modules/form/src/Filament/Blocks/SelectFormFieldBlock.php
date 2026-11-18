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
use AidingApp\Form\Filament\Forms\StateCasts\OptionsStateCast;
use AidingApp\Form\Models\SubmissibleField;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class SelectFormFieldBlock extends FormFieldBlock
{
    public static function type(): string
    {
        return 'select';
    }

    /**
     * @return array<Component>
     */
    public static function fields(): array
    {
        return [
            TextInput::make('placeholder')
                ->string(),
            OptionsKeyValue::make('options')
                ->required(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getFormKitSchema(SubmissibleField $field): array
    {
        return [
            '$formkit' => 'select',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
            'placeholder' => $field->config['placeholder'] ?? null,
            'options' => (new OptionsStateCast())->get($field->config['options']),

            ...self::getDescriptionSectionsSchema($field),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        $values = collect((new OptionsStateCast())->get($field->config['options']))->pluck('value');

        return [
            'string',
            'in:' . $values->join(','),
        ];
    }

    /**
     * The editor ignores key order when comparing a block's config, so reordering options alone would leave a stale edit button.
     * A hidden marker of the order changes the preview, which forces the block to refresh.
     */
    public static function toPreviewHtml(array $config): ?string
    {
        $values = collect((new OptionsStateCast())->get($config['options'] ?? []))->pluck('value')->all();
        $order = e(json_encode($values));

        return parent::toPreviewHtml($config) . "<span hidden data-options-order=\"{$order}\"></span>";
    }

    protected static function renderedView(): string
    {
        return 'form::blocks.submissions.select';
    }
}
