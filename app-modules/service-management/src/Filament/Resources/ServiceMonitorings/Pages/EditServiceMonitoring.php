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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\Pages;

use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Filament\Actions\ResetAction;
use AidingApp\ServiceManagement\Filament\Components\AutomatedReportingSection;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\Schemas\Components\ConfidentialitySection;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Rules\ValidServiceMonitoringKeywordValues;
use App\Features\MonitorTypeFeature;
use App\Filament\Forms\Components\UserSelect;
use App\Rules\ValidUrl;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EditServiceMonitoring extends EditRecord
{
    protected static string $resource = ServiceMonitoringResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Monitor Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description')
                            ->string()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        TextInput::make('domain')
                            ->label('URL')
                            ->required()
                            ->maxLength(255)
                            ->rules([new ValidUrl()])
                            ->columnSpan(1),
                        Select::make('frequency')
                            ->label('Frequency')
                            ->searchable()
                            ->options(ServiceMonitoringFrequency::class)
                            ->enum(ServiceMonitoringFrequency::class)
                            ->required()
                            ->columnSpan(1),
                        Radio::make('monitor_type')
                            ->label('Monitor Type')
                            ->options(MonitorType::class)
                            ->enum(MonitorType::class)
                            ->default(MonitorType::Availability)
                            ->live()
                            ->inline()
                            ->visible(MonitorTypeFeature::active())
                            ->columnSpanFull(),
                        TextEntry::make('helperText')
                            ->hiddenLabel()
                            ->state('Spaces may be used within a string. Use quotes when a string contains a comma or double quotes.')
                            ->visible(fn (Get $get): bool => $get('monitor_type') === MonitorType::KeywordMatch && MonitorTypeFeature::active())
                            ->columnSpanFull(),
                        TextInput::make('should_contain')
                            ->label('Should Contain')
                            ->formatStateUsing(fn (?array $state): ?string => filled($state)
                                ? collect($state)
                                    ->map(fn (string $value): string => str_contains($value, ',') || str_contains($value, '"') ? "\"{$value}\"" : $value)
                                    ->implode(', ')
                                : null)
                            ->rules(fn (Get $get): array => [
                                ...($get('monitor_type') === MonitorType::KeywordMatch ? ['required_without:data.should_not_contain'] : []),
                                new ValidServiceMonitoringKeywordValues(),
                            ])
                            ->visible(fn (Get $get): bool => $get('monitor_type') === MonitorType::KeywordMatch && MonitorTypeFeature::active())
                            ->hintIcon('heroicon-m-question-mark-circle', 'Enter one or more required strings separated by commas. Every string must appear in the response. Matching is case-insensitive.'),
                        TextInput::make('should_not_contain')
                            ->label('Should Not Contain')
                            ->formatStateUsing(fn (?array $state): ?string => filled($state)
                                ? collect($state)
                                    ->map(fn (string $value): string => str_contains($value, ',') || str_contains($value, '"') ? "\"{$value}\"" : $value)
                                    ->implode(', ')
                                : null)
                            ->rules(fn (Get $get): array => [
                                ...($get('monitor_type') === MonitorType::KeywordMatch ? ['required_without:data.should_contain'] : []),
                                new ValidServiceMonitoringKeywordValues(),
                            ])
                            ->visible(fn (Get $get): bool => $get('monitor_type') === MonitorType::KeywordMatch && MonitorTypeFeature::active())
                            ->hintIcon('heroicon-m-question-mark-circle', 'Enter one or more prohibited strings separated by commas. The check fails if any string appears in the response. Matching is case-insensitive.'),
                    ])
                    ->columns(2),
                Section::make('Notification Settings')
                    ->schema([
                        Select::make('department')
                            ->relationship('departments', 'name')
                            ->label('Department')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        UserSelect::make('user')
                            ->relationship('users')
                            ->label('User')
                            ->multiple()
                            ->preload(),
                        Toggle::make('is_notified_via_database')
                            ->label('In Product notifications')
                            ->default(false),
                        Toggle::make('is_notified_via_email')
                            ->label('Email Notifications')
                            ->default(false),
                    ])
                    ->columns(2),
                AutomatedReportingSection::make(),
                ConfidentialitySection::make(
                    notifiedUsersField: 'user',
                    notifiedDepartmentsField: 'department',
                ),
            ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var ServiceMonitoringTarget $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (['should_contain', 'should_not_contain'] as $field) {
            if (filled($data[$field] ?? null)) {
                $values = array_map('trim', str_getcsv($data[$field]));
                    $data[$field] = ValidServiceMonitoringKeywordValues::parseValues($data[$field]);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var ServiceMonitoringTarget $record */
        $record = $this->getRecord();

        if (! $record->wasChanged('is_confidential') || $record->is_confidential) {
            return;
        }

        // Filament skips saving hidden relationship fields, so clear stale grants explicitly
        $record->confidentialUsers()->sync([]);
        $record->confidentialDepartments()->sync([]);
        $record->confidentialContacts()->sync([]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            ResetAction::make(),
            DeleteAction::make(),
        ];
    }
}
