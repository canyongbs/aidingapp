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

namespace AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages;

use AidingApp\ServiceManagement\Filament\Resources\Advisories\AdvisoryResource;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\RelationManagers\AdvisoryUpdatesRelationManager;
use AidingApp\ServiceManagement\Filament\Tables\DepartmentsTable;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TableSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ViewAdvisory extends ViewRecord
{
    protected static string $resource = AdvisoryResource::class;

    protected static ?string $navigationLabel = 'View';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Properties')
                    ->key('properties')
                    ->headerActions([
                        EditAction::make('editProperties')
                            ->slideOver()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->string()
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->maxLength(65535)
                                    ->string()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),
                Section::make('Tracking Details')
                    ->key('trackingDetails')
                    ->headerActions([
                        EditAction::make('editTrackingDetails')
                            ->slideOver()
                            ->schema([
                                ToggleButtons::make('severity_id')
                                    ->label('Severity')
                                    ->inline()
                                    ->options(fn (): array => AdvisorySeverity::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->exists((new AdvisorySeverity())->getTable(), 'id')
                                    ->required()
                                    ->columnSpanFull(),
                                ToggleButtons::make('status_id')
                                    ->label('Status')
                                    ->inline()
                                    ->options(fn (): array => AdvisoryStatus::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->exists((new AdvisoryStatus())->getTable(), 'id')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->schema([
                        ColorEntry::make('severity.rgb_color')
                            ->label('Severity')
                            ->tooltip(fn (Advisory $record) => $record->severity->name)
                            ->columnSpan(1),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                Section::make('Assignment')
                    ->key('assignment')
                    ->headerActions([
                        EditAction::make('editAssignment')
                            ->slideOver()
                            ->schema([
                                TableSelect::make('assigned_department_id')
                                    ->label('Department')
                                    ->relationship('assignedDepartment')
                                    ->tableConfiguration(DepartmentsTable::class)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->schema([
                        TextEntry::make('assignedDepartment.name')
                            ->label('Department'),
                    ]),
                Section::make('Advisory Updates')
                    ->schema([
                        Livewire::make(AdvisoryUpdatesRelationManager::class, fn (Advisory $record): array => [
                            'ownerRecord' => $record,
                            'pageClass' => static::class,
                        ])->key(AdvisoryUpdatesRelationManager::class),
                    ]),
            ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var Advisory $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->title, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
