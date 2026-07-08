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

namespace AidingApp\Project\Filament\Resources\Pipelines\Forms;

use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Tables\PipelineEntryAssignToTable;
use AidingApp\Project\Filament\Tables\PipelineEntryRelatedToTable;
use App\Features\PipelineEntryFieldsFeature;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\TableSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class PipelineEntryForm
{
    /**
     * Returns the shared form components common to create and edit pipeline entry forms.
     *
     * @return array<int, mixed>
     */
    public static function components(): array
    {
        return [
            MorphToSelect::make('organizable')
                ->types([
                    Type::make(Contact::class)
                        ->label('Contact')
                        ->titleAttribute('full_name')
                        ->modifyOptionsQueryUsing(fn (Builder $query) => $query->limit(50)),
                ])
                ->searchable()
                ->preload()
                ->required(),
            Textarea::make('description')
                ->visible(fn () => PipelineEntryFieldsFeature::active())
                ->maxLength(65535),
            DateTimePicker::make('due')
                ->label('Due Date')
                ->visible(fn () => PipelineEntryFieldsFeature::active()),
            ToggleButtons::make('assigned_to_type')
                ->label('Assigned To')
                ->visible(fn () => PipelineEntryFieldsFeature::active())
                ->options(['none' => 'None', 'user' => 'User'])
                ->inline()
                ->live()
                ->default('none')
                ->dehydrated(false),
            TableSelect::make('assigned_to')
                ->hiddenLabel()
                ->relationship('assignedTo')
                ->tableConfiguration(PipelineEntryAssignToTable::class)
                ->visible(fn (Get $get) => PipelineEntryFieldsFeature::active() && $get('assigned_to_type') === 'user')
                ->required(fn (Get $get) => PipelineEntryFieldsFeature::active() && $get('assigned_to_type') === 'user')
                ->rules([Rule::exists('users', 'id')]),
            ToggleButtons::make('related_to_type')
                ->label('Related To')
                ->visible(fn () => PipelineEntryFieldsFeature::active())
                ->options(['none' => 'None', 'contact' => 'Contact'])
                ->inline()
                ->live()
                ->default('none')
                ->dehydrated(false),
            TableSelect::make('related_to')
                ->hiddenLabel()
                ->relationship('relatedTo')
                ->tableConfiguration(PipelineEntryRelatedToTable::class)
                ->visible(fn (Get $get) => PipelineEntryFieldsFeature::active() && $get('related_to_type') === 'contact')
                ->required(fn (Get $get) => PipelineEntryFieldsFeature::active() && $get('related_to_type') === 'contact')
                ->rules([Rule::exists('contacts', 'id')]),
        ];
    }
}
