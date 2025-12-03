<?php

namespace AidingApp\Project\Filament\Resources\PipelineResource\Pages;

use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\Project;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class CreatePipeline extends CreateRecord
{
    protected static string $resource = PipelineResource::class;

    #[Locked, Url]
    public ?string $project = null;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                Repeater::make('stages')
                    ->relationship('stages')
                    ->schema([
                        TextInput::make('name')
                            ->label('Stage')
                            ->distinct()
                            ->required(),
                    ])
                    ->orderColumn('order')
                    ->reorderable()
                    ->columnSpanFull()
                    ->label('Pipeline Stages')
                    ->minItems(1)
                    ->maxItems(5),
            ]);
    }

    public function getBreadcrumbs(): array
    {
        $project = Project::find($this->project);

        $breadcrumbs = [
            ProjectResource::getUrl() => ProjectResource::getBreadcrumb(),
            ...($project ? [
                ProjectResource::getUrl('view', ['record' => $project]) => $project->name ?? '',
                ProjectResource::getUrl('manage-pipelines', ['record' => $project]) => 'Pipelines',
            ] : []),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $project = Project::find($this->project);

        if ($project && (! auth()->user()->can('update', $project))) {
            $project = null;
        }

        $data['user_id'] = auth()->id();
        $data['project_id'] = $project?->getKey();

        return $data;
    }

    protected function getCancelFormAction(): Action
    {
        $project = Project::find($this->project);

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from($this->previousUrl ?? ($project ? ProjectResource::getUrl('manage-pipelines', ['record' => $project]) : null)) . ')')
            ->color('gray');
    }
}
