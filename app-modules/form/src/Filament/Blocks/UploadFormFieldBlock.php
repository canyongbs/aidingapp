<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Form\Filament\Blocks;

use Filament\Forms\Get;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use AidingApp\Form\Models\SubmissibleField;

class UploadFormFieldBlock extends FormFieldBlock
{
    public ?string $icon = 'heroicon-m-document-arrow-up';

    //Don't use in filament
    public static bool $internal = true;

    public static function type(): string
    {
        return 'upload';
    }

    public function fields(): array
    {
        return [
            // Checkbox::make('multiple')
            //     ->live(),
            // TextInput::make('limit')
            //     ->integer()
            //     ->minValue(2)
            //     ->default(2)
            //     ->visible(fn (Get $get) => $get('multiple')),
            // TagsInput::make('accept')
            //     ->label('Accepted File Types')
            //     ->placeholder('e.g. .pdf, .docx, .jpg'),
            // TextInput::make('size')
            //     ->label('Maximum File Size')
            //     ->integer()
            //     ->suffix('MB'),
        ];
    }

    public static function getFormKitSchema(SubmissibleField $field): array
    {
        return [
            '$formkit' => 'upload',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
            'multiple' => $field->config['multiple'] ?? false,
            'accept' => $field->config['accept'] ?? '',
            'limit' => $field->config['limit'] ?? null,
            'size' => $field->config['size'] ?? null,
            'uploadUrl' => route('api.portal.knowledge-management.service-request.request-upload-url'),
        ];
    }

    public static function getValidationRules(SubmissibleField $field): array
    {
        return [];
    }
}
