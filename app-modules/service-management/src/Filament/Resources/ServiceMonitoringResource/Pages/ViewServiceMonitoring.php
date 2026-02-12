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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages;

use AidingApp\ServiceManagement\Filament\Actions\ResetAction;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Widgets\ServiceUptimeWidget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ViewServiceMonitoring extends ViewRecord
{
    protected static string $resource = ServiceMonitoringResource::class;

    protected static ?string $navigationLabel = 'View';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('description')
                            ->label('Description'),
                        TextEntry::make('domain')
                            ->label('URL'),
                        TextEntry::make('frequency')
                            ->label('Frequency'),
                        Section::make('Notification Settings')
                            ->schema([
                                TextEntry::make('teams.name')
                                    ->label('Teams')
                                    ->listWithLineBreaks()
                                    ->limitList(3)
                                    ->expandableLimitedList()
                                    ->visible(function () {
                                        assert($this->getRecord() instanceof ServiceMonitoringTarget);

                                        return $this->getRecord()->teams()->count();
                                    }),
                                TextEntry::make('users.name')
                                    ->label('Users')
                                    ->listWithLineBreaks()
                                    ->limitList(3)
                                    ->expandableLimitedList()
                                    ->visible(function () {
                                        assert($this->getRecord() instanceof ServiceMonitoringTarget);

                                        return $this->getRecord()->users()->count();
                                    }),
                                IconEntry::make('is_notified_via_database')
                                    ->label('In Product notifications')
                                    ->boolean(),
                                IconEntry::make('is_notified_via_email')
                                    ->label('Email Notifications')
                                    ->boolean(),
                            ])
                            ->visible(function (): bool {
                                assert($this->getRecord() instanceof ServiceMonitoringTarget);

                                return $this->getRecord()->teams()->count() || $this->getRecord()->users()->count();
                            })
                            ->columns(),
                    ])
                    ->columns(),
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

    protected function getHeaderActions(): array
    {
        return [
            ResetAction::make(),
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ServiceUptimeWidget::class,
        ];
    }
}
