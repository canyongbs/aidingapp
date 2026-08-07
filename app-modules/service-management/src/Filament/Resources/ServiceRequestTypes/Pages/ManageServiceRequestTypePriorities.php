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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Filament\Resources\SLAs\SlaResource;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ManageServiceRequestTypePriorities extends ManageRelatedRecords
{
    use InteractsWithForms;

    protected static string $resource = ServiceRequestTypeResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'priorities';

    protected static ?string $navigationLabel = 'Priorities';

    protected static ?string $breadcrumb = 'Priorities';

    protected string $view = 'service-management::filament.resources.service-request-type-resource.pages.manage-service-request-type-priorities';

    /** @var array<string, mixed>|null */
    public ?array $configurationData = [];

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $type = $this->getServiceRequestType();

        $this->getConfigurationForm()->fill([
            'customer_defined_priorities' => ! $type->defaultPriority()->exists(),
            'default_priority_id' => $type->defaultPriority()->value('id'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string()
                    ->unique(
                        table: 'service_request_priorities',
                        column: 'name',
                        ignorable: fn (?Model $record): ?Model => $record,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('type_id', $this->getOwnerRecord()->getKey())->withoutTrashed(),
                    ),
                TextInput::make('order')
                    ->label('Priority Order')
                    ->required()
                    ->integer()
                    ->numeric()
                    ->disabledOn('edit'),
                Select::make('sla_id')
                    ->label('SLA')
                    ->relationship('sla', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema) => SlaResource::form($schema)),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Priority Order')
                    ->sortable(),
                TextColumn::make('sla.name')
                    ->label('SLA')
                    ->url(fn (ServiceRequestPriority $record): ?string => $record->sla ? SlaResource::getUrl('edit', ['record' => $record->sla]) : null)
                    ->searchable(),
                TextColumn::make('service_requests_count')
                    ->label('# of Service Requests')
                    ->counts('serviceRequests')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->paginated(false)
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public function configurationForm(Schema $schema): Schema
    {
        return $schema
            ->model($this->getOwnerRecord())
            ->statePath('configurationData')
            ->components([
                Section::make('Priorities Configuration')
                    ->schema([
                        Section::make('Customer Defined Priorities')
                            ->description('Allow customers to define the priority of their requests in the portal and in the Support Assistant experiences.')
                            ->compact()
                            ->schema([
                                Toggle::make('customer_defined_priorities')
                                    ->label('Enable')
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (bool $state, Set $set) => $state
                                            ? $set('default_priority_id', null)
                                            : null
                                    ),
                            ]),

                        Select::make('default_priority_id')
                            ->label('Default Priority')
                            ->helperText(
                                'Define a default priority for all new service requests.'
                            )
                            ->options(
                                fn (): array => $this->getServiceRequestType()
                                    ->priorities()
                                    ->orderBy('order')
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->rule(Rule::exists('service_request_priorities', 'id')->where('type_id', $this->getServiceRequestType()->getKey()))
                            ->required(fn (Get $get): bool => ! $get('customer_defined_priorities'))
                            ->hidden(fn (Get $get): bool => $get('customer_defined_priorities')),
                    ]),
            ]);
    }

    public function savePriorityConfiguration(): void
    {
        $data = $this->getConfigurationForm()->getState();

        DB::transaction(function () use ($data): void {
            $type = $this->getServiceRequestType();

            $type->update([
                'default_priority_id' => $data['customer_defined_priorities']
                    ? null
                    : $data['default_priority_id'],
            ]);
        });

        Notification::make()
            ->title('Configuration saved')
            ->success()
            ->send();
    }

    protected function getConfigurationForm(): Schema
    {
        $form = $this->getSchema('configurationForm');

        assert($form instanceof Schema);

        return $form;
    }

    protected function getServiceRequestType(): ServiceRequestType
    {
        $type = $this->getOwnerRecord();

        assert($type instanceof ServiceRequestType);

        return $type;
    }
}
