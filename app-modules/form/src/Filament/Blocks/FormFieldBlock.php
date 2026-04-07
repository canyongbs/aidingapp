<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Form\Models\SubmissibleField;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

abstract class FormFieldBlock extends RichContentCustomBlock
{
    public static string $preview = 'form::blocks.previews.default';

    public static string $rendered = 'form::blocks.submissions.default';

    public static bool $internal = false;

    abstract public static function type(): string;

    public static function getId(): string
    {
        return static::type();
    }

    public static function getLabel(): string
    {
        return (string) str(static::getId())
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            TextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255),
            Checkbox::make('isRequired')
                ->label('Required'),
            ...static::fields(),
        ]);
    }

    public static function getPreviewLabel(array $config): string
    {
        return $config['label'] ?? static::getLabel();
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return view(static::$preview, $config)->render();
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return view(static::$rendered, [...$config, ...$data])->render();
    }

    public static function fields(): array
    {
        return [];
    }

    abstract public static function getFormKitSchema(SubmissibleField $field): array;

    public static function getValidationRules(SubmissibleField $field): array
    {
        return [];
    }

    public static function getSubmissionState(mixed $response): array
    {
        return [
            'response' => $response,
        ];
    }
}
