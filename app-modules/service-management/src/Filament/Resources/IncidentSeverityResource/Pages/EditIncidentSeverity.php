<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use App\Concerns\EditPageRedirection;
use App\Filament\Forms\Components\ColorSelect;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditIncidentSeverity extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = IncidentSeverityResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->string(),
                ColorSelect::make('color')
                    ->label('Color')
                    ->required(),
            ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var IncidentSeverity $record */
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
