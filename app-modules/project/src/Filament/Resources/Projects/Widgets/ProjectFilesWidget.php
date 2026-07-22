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

namespace AidingApp\Project\Filament\Resources\Projects\Widgets;

use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectFile;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;

class ProjectFilesWidget extends TableWidget
{
    #[Locked]
    public Project $record;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'project::filament.resources.projects.widgets.project-files-widget';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->can('viewAny', Project::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->record->files()->getQuery())
            ->recordTitleAttribute('description')
            ->heading('Project Files')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                IdColumn::make(),
                TextColumn::make('description'),
                IconColumn::make('media')
                    ->tooltip(fn (ProjectFile $record) => match ($record->getMedia('file')->first()?->mime_type) {
                        default => 'File',
                        'image/png' => 'Image (.png)',
                        'image/jpeg' => 'Image (.jpeg)',
                        'image/gif' => 'Image (.gif)',
                        'application/pdf' => 'PDF',
                        'application/msword' => 'Document',
                        'text/csv' => 'CSV',
                        'application/vnd.ms-excel' => 'Spreadsheet',
                        'application/msexcel' => 'Spreadsheet',
                        'application/ms-excel' => 'Spreadsheet',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Spreadsheet',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'Presentation',
                        'text/plain' => 'Text File',
                        'audio/mpeg' => 'MP3',
                        'video/mp4' => 'MP4',
                        'application/zip' => 'Zip File',
                    })
                    ->icon(fn (ProjectFile $record) => match ($record->getMedia('file')->first()?->mime_type) {
                        default => 'heroicon-m-paper-clip',
                        'image/png' => 'heroicon-m-photo',
                        'image/jpeg' => 'heroicon-m-camera',
                        'image/gif' => 'heroicon-m-gif',
                        'application/pdf' => 'heroicon-m-document-text',
                        'application/msword' => 'heroicon-m-document-text',
                        'text/csv' => 'heroicon-m-table-cells',
                        'application/vnd.ms-excel' => 'heroicon-m-table-cells',
                        'application/msexcel' => 'heroicon-m-table-cells',
                        'application/ms-excel' => 'heroicon-m-table-cells',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'heroicon-m-document-text',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'heroicon-m-table-cells',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'heroicon-m-presentation-chart-bar',
                        'text/plain' => 'heroicon-m-document-text',
                        'audio/mpeg' => 'heroicon-m-musical-note',
                        'video/mp4' => 'heroicon-m-video-camera',
                        'application/zip' => 'heroicon-m-archive-box',
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
                TextColumn::make('retention_date')
                    ->label('Retention Date')
                    ->placeholder('N/A')
                    ->dateTime(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->placeholder('N/A'),
            ])
            ->recordActions([
                Action::make('download')
                    ->icon('heroicon-m-arrow-down-on-square')
                    ->color('info')
                    ->visible(fn (ProjectFile $record): bool => filled($record->getMedia('file')->first()?->getPathRelativeToRoot()))
                    ->action(
                        fn (ProjectFile $record) => Storage::disk('s3')
                            ->download(
                                $record
                                    ->getMedia('file')
                                    ->first()
                                    ?->getPathRelativeToRoot()
                            )
                    ),
            ])
            ->headerActions([
                Action::make('manageFiles')
                    ->label('Manage')
                    ->authorize('create', $this->record)
                    ->color('gray')
                    ->url(fn (): string => ProjectResource::getUrl('manage-files', ['record' => $this->record])),
            ]);
    }
}
