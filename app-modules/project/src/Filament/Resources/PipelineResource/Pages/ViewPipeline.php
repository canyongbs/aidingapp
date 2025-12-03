<?php

namespace AidingApp\Project\Filament\Resources\PipelineResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use AidingApp\Project\Models\Pipeline;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Filament\Resources\PipelineResource;

class ViewPipeline extends ViewRecord
{
    protected static string $resource = PipelineResource::class;

    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                TextEntry::make('name'),
                TextEntry::make('description'),
                RepeatableEntry::make('stages')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Stage'),
                    ])
                    ->label('Other stages')
                    ->columns(2),
            ]),
        ]);
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $pipeline = $this->getRecord();

        assert($pipeline instanceof Pipeline);

        $project = $pipeline->project;

        $breadcrumbs = [
            ProjectResource::getUrl() => ProjectResource::getBreadcrumb(),
            ...($project ? [
                ProjectResource::getUrl('view', ['record' => $project]) => $project->name ?? '',
                ProjectResource::getUrl('manage-pipelines', ['record' => $project]) => 'Pipelines',
            ] : []),
            PipelineResource::getUrl('view', ['record' => $this->getRecord()]) => Str::limit($this->getRecordTitle(), 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }
}