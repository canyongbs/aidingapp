<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages;

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditIncidentStatus extends EditRecord
{
    protected static string $resource = IncidentStatusResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
                Select::make('classification')
                    ->label('Classification')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(SystemIncidentStatusClassification::class)
                    ->enum(SystemIncidentStatusClassification::class),
            ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var IncidentStatus $record */
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
