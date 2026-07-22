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

namespace AidingApp\Project\Filament\Resources\Projects\Forms;

use AidingApp\Project\Models\Project;
use App\Filament\Forms\Components\IconSelect;
use CanyonGBS\Common\Enums\Color;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Validation\Rule;

class ProjectForm
{
    /**
     * @return array<int, mixed>
     */
    public static function components(bool $isEdit = false): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->string()
                ->unique(ignoreRecord: $isEdit)
                ->maxLength(255),
            Textarea::make('description')
                ->string()
                ->maxLength(65535)
                ->columnSpanFull(),
            IconSelect::make('icon')
                ->required()
                ->visible()
                ->default('heroicon-o-clipboard-document-list'),
            ColorSelect::make('color')
                ->required()
                ->visible()
                ->rules([Rule::in(array_map(fn ($color) => $color->value, Color::cases()))])
                ->default('gray'),
            Select::make('department_id')
                ->relationship('department', 'name')
                ->preload()
                ->visible()
                ->searchable()
                ->nullable(),
            DatePicker::make('start_date')
                ->visible()
                ->nullable(),
            ToggleButtons::make('target_completion_date_type')
                ->label('Target Completion Date')
                ->visible()
                ->options(['indefinite' => 'Indefinite', 'set' => 'Set'])
                ->default('indefinite')
                ->live()
                ->inline()
                ->columnSpanFull()
                ->dehydrated(false)
                ->afterStateHydrated(function (Set $set, ?Project $record) {
                    if ($record && filled($record->target_completion_date)) {
                        $set('target_completion_date_type', 'set');
                    } else {
                        $set('target_completion_date_type', 'indefinite');
                    }
                })
                ->afterStateUpdated(function (string $state, callable $set) {
                    if ($state === 'indefinite') {
                        $set('target_completion_date', null);
                    }
                }),
            DatePicker::make('target_completion_date')
                ->hiddenLabel()
                ->visible(fn (Get $get) => $get('target_completion_date_type') === 'set')
                ->nullable(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    public static function mutateDataForSave(array $data): array
    {
        if (! array_key_exists('target_completion_date', $data)) {
            $data['target_completion_date'] = null;
        }

        return $data;
    }
}
