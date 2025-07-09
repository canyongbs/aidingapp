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

namespace AidingApp\Task\Filament\Resources\TaskResource\Pages;

use AidingApp\Task\Filament\Concerns\TaskForm;
use AidingApp\Task\Filament\Resources\TaskResource;
use AidingApp\Task\Models\Task;
use App\Features\ConfidentialTaskFeature;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateTask extends CreateRecord
{
    use TaskForm;

    protected static string $resource = TaskResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Confidentiality')
                    ->visible(ConfidentialTaskFeature::active())
                    ->schema([
                        Checkbox::make('is_confidential')
                            ->label('Confidential')
                            ->live()
                            ->columnSpanFull(),
                        Select::make('task_confidential_users')
                            ->relationship('confidentialAccessUsers', 'name')
                            ->preload()
                            ->label('Users')
                            ->multiple()
                            ->exists('users', 'id')
                            ->visible(fn (Get $get) => $get('is_confidential')),
                        Select::make('task_confidential_teams')
                            ->relationship('confidentialAccessTeams', 'name')
                            ->preload()
                            ->label('Teams')
                            ->multiple()
                            ->exists('teams', 'id')
                            ->visible(fn (Get $get) => $get('is_confidential')),
                    ]),
                TextInput::make('title')
                    ->required()
                    ->maxLength(100)
                    ->string(),
                Textarea::make('description')
                    ->required()
                    ->string(),
                DateTimePicker::make('due')
                    ->label('Due Date')
                    ->native(false),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name', $this->scopeAssignmentRelationshipBasedOnConcern())
                    ->nullable()
                    ->searchable(['name', 'email'])
                    ->default(auth()->id()),
                Select::make('concern_id')
                    ->label('Related To')
                    ->relationship('concern', 'first_name')
                    ->nullable()
                    ->afterStateUpdated($this->updateAssignmentAfterConcernSelected()),
            ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new Task(Arr::except($data, 'assigned_to'));
        $record->assigned_to = $data['assigned_to'] ?? null;
        $record->save();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return TaskResource::getUrl();
    }
}
