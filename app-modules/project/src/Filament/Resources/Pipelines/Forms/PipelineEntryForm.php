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
use AidingApp\Project\Models\Pipeline;
use App\Features\PipelineEntryEnhancedFieldsFeature;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;

class PipelineEntryForm
{
    /**
     * Returns the shared form components common to create and edit pipeline entry forms.
     *
     * @return array<int, mixed>
     */
    public static function components(?Pipeline $pipeline = null): array
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
                ->maxLength(65535),
            DateTimePicker::make('due')
                ->label('Due Date'),
            MorphToSelect::make('assignedTo')
                ->label('Assigned To')
                ->types([
                    Type::make(User::class)
                        ->label('User')
                        ->titleAttribute('name')
                        ->modifyOptionsQueryUsing(fn (Builder $query) => $query->limit(50)),
                    Type::make(Contact::class)
                        ->label('Contact')
                        ->titleAttribute('full_name')
                        ->modifyOptionsQueryUsing(fn (Builder $query) => $query->limit(50)),
                ])
                ->searchable()
                ->preload()
                ->typeSelectToggleButtons()
                ->visible(fn () => PipelineEntryEnhancedFieldsFeature::active()),
            Toggle::make('is_visible_to_guests')
                ->label('Visible to Guest')
                ->visible(fn () => PipelineEntryEnhancedFieldsFeature::active())
                ->default(true),
            Select::make('milestones')
                ->label('Related Milestones')
                ->relationship(
                    name: 'milestones',
                    titleAttribute: 'title',
                    modifyQueryUsing: $pipeline
                        ? fn (Builder $query) => $query->where('project_id', $pipeline->project_id)
                        : null,
                )
                ->multiple()
                ->searchable()
                ->preload()
                ->visible(fn () => PipelineEntryEnhancedFieldsFeature::active()),
            Select::make('assets')
                ->label('Related Assets')
                ->relationship(name: 'assets', titleAttribute: 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->visible(fn () => PipelineEntryEnhancedFieldsFeature::active()),
            Select::make('serviceRequests')
                ->label('Related Service Requests')
                ->relationship(name: 'serviceRequests', titleAttribute: 'title')
                ->multiple()
                ->searchable()
                ->preload()
                ->visible(fn () => PipelineEntryEnhancedFieldsFeature::active()),
        ];
    }
}
