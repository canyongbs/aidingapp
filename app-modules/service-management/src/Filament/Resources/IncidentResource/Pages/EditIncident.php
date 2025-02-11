<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Models\Incident;
use App\Concerns\EditPageRedirection;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditIncident extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = IncidentResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->string(),
                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(65535)
                    ->string(),
                Select::make('severity_id')
                    ->label('Severity')
                    ->preload()
                    ->required()
                    ->searchable()
                    ->relationship('severity', 'name'),
                Select::make('status_id')
                    ->label('Status')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->relationship('status', 'name'),
                Select::make('assigned_team_id')
                    ->label('Assigned Team')
                    ->preload()
                    ->searchable()
                    ->relationship('assignedTeam', 'name'),
            ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var Incident $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $record]) => Str::limit($record->title, 16),
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
