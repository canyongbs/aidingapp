<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AidingApp\Form\Models\SubmissibleField;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput as FilamentTextInput;

class EducatableEmailFormFieldBlock extends FormFieldBlock
{
    public ?string $label = 'Filler email address';

    public string $rendered = 'form::blocks.submissions.educatable-email';

    public ?string $icon = 'heroicon-m-user';

    public static function type(): string
    {
        return 'educatable_email';
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('Your email address'),
            Checkbox::make('isRequired')
                ->label('Required')
                ->default(true),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function getFormKitSchema(SubmissibleField $field): array
    {
        return [
            '$formkit' => 'email',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        return ['string', 'email', 'max:255'];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubmissionState(mixed $response): array
    {
        $author = app(ResolveSubmissionAuthorFromEmail::class)($response);

        return [
            'response' => $response,
            'authorKey' => $author ? $author->getKey() : null,
            'authorType' => $author ? $author::class : null,
        ];
    }
}
