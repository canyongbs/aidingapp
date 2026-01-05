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

namespace AidingApp\Project\Filament\Resources\PipelineResource\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Filament\Resources\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class ViewPipelineEntry extends Page
{
    protected static string $resource = PipelineResource::class;

    protected static ?string $title = 'Pipeline Entry Details';

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static string $view = 'project::filament.pages.view-pipeline-entry';

    public Pipeline $record;

    public PipelineEntry $pipelineEntry;

    #[Locked, Url]
    public ?string $project = null;

    public function mount(): void
    {
        if ($this->pipelineEntry->pipelineStage->pipeline_id !== $this->record->id) {
            abort(404);
        }

        if (request()->has('from')) {
            session(['pipeline_entry_source' => request('from')]);
        }
    }

    public function getTitle(): string | Htmlable
    {
        return 'Pipeline Entry Details';
    }

    public function getBackUrl(): string
    {
        $source = session('pipeline_entry_source', 'list');

        $params = ['record' => $this->record];

        if ($this->project) {
            $params['project'] = $this->project;
        }

        if ($source === 'kanban') {
            return PipelineResource::getUrl('manage-entries', $params);
        }

        return PipelineResource::getUrl('manage-entries', $params);
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $pipeline = $this->record;
        $project = $pipeline->project;

        $breadcrumbs = [
            ProjectResource::getUrl() => ProjectResource::getBreadcrumb(),
            ...($project ? [
                ProjectResource::getUrl('view', ['record' => $project]) => $project->name ?? '',
                ProjectResource::getUrl('manage-pipelines', ['record' => $project]) => 'Pipelines',
            ] : []),
            PipelineResource::getUrl('view', ['record' => $this->record]) => Str::limit('Pipelines', 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function entryDetailsInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->pipelineEntry)
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name')
                            ->extraAttributes(['class' => 'break-words']),
                        TextEntry::make('organizable.full_name')
                            ->label('Organization Name')
                            ->url(function (PipelineEntry $record) {
                                return match ($record->organizable->getMorphClass()) {
                                    app(Contact::class)->getMorphClass() => ContactResource::getUrl('view', ['record' => $record->organizable_id]),
                                    default => null,
                                };
                            })
                            ->openUrlInNewTab(),
                        TextEntry::make('organizable_type')
                            ->label('Organization Type')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->badge(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->url(function (): string {
                    $params = [
                        'record' => $this->record,
                        'pipelineEntry' => $this->pipelineEntry,
                        'project' => $this->project,
                    ];

                    return PipelineResource::getUrl('edit-pipeline-entry', $params);
                }),
            DeleteAction::make()
                ->label('Remove from Pipeline')
                ->requiresConfirmation()
                ->record($this->pipelineEntry)
                ->successRedirectUrl(fn (): string => $this->getBackUrl()),
        ];
    }
}
