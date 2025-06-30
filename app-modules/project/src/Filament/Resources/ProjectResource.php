<?php

namespace AidingApp\Project\Filament\Resources;

use AidingApp\Project\Filament\Resources\ProjectResource\Pages\CreateProject;
use AidingApp\Project\Filament\Resources\ProjectResource\Pages\EditProject;
use AidingApp\Project\Filament\Resources\ProjectResource\Pages\ListProjects;
use AidingApp\Project\Filament\Resources\ProjectResource\Pages\ViewProject;
use AidingApp\Project\Models\Project;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?int $navigationSort = 10;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProject::class,
            EditProject::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
