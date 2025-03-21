<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Concerns\EditPageRedirection;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditServiceMonitoring extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ServiceMonitoringResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->string()
                    ->maxLength(65535),
                TextInput::make('domain')
                    ->label('URL')
                    ->required()
                    ->maxLength(255)
                    ->url(),
                Select::make('frequency')
                    ->label('Frequency')
                    ->searchable()
                    ->options(ServiceMonitoringFrequency::class)
                    ->enum(ServiceMonitoringFrequency::class)
                    ->required(),
                Section::make('Notification Group')
                    ->schema([
                        Select::make('team')
                            ->relationship('teams', 'name')
                            ->label('Team')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Select::make('user')
                            ->relationship('users', 'name')
                            ->label('User')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),
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
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
