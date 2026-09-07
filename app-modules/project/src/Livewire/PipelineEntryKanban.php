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

namespace AidingApp\Project\Livewire;

use AidingApp\Project\Filament\Resources\Pipelines\Actions\EditPipelineEntryAction;
use AidingApp\Project\Filament\Resources\Pipelines\Actions\ViewPipelineEntryAction;
use AidingApp\Project\Filament\Resources\Pipelines\Forms\PipelineEntryForm;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PipelineEntryKanban extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Pipeline $pipeline;

    public ?string $project = null;

    public function mount(Pipeline $pipeline, ?string $project = null): void
    {
        $this->pipeline = $pipeline;
        $this->project = $project;
    }

    /**
     * @return Collection<string, Collection<int, PipelineEntry>>
     */
    public function getPipelineEntries(): Collection
    {
        /**
         * @var Collection<int, PipelineEntry> $entries
         */
        $entries = $this->pipeline->entries()
            ->withoutArchived()
            ->with(['assignedTo', 'pipelineStage', 'milestone'])
            ->withCount([
                'assets',
                'serviceRequests',
            ])
            ->oldest()
            ->get();

        return $entries->groupBy(function (PipelineEntry $entry): string {
            /** @var int|string|null $stageId */
            $stageId = $entry->pipeline_stage_id;

            return (string) $stageId;
        });
    }

    /**
     * @return Collection<string, mixed>
     */
    public function getStages(): Collection
    {
        return $this->pipeline->stages()
            ->orderBy('order')
            ->pluck('name', 'id');
    }

    public function movedEntry(string $pipeline, string $entryId, string $fromStage = '', string $toStage = ''): JsonResponse
    {
        $this->authorize('update', $this->pipeline);

        try {
            $stage = $this->pipeline->stages()->whereKey($toStage)->firstOrFail();

            $this->pipeline->entries()
                ->whereKey($entryId)
                ->where('pipeline_stage_id', $fromStage)
                ->firstOrFail()
                ->update([
                    'pipeline_stage_id' => $stage->getKey(),
                ]);
        } catch (Exception $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Pipeline task could not be moved. Something went wrong, if this continues please contact support.',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pipeline stage updated successfully.',
        ], ResponseAlias::HTTP_OK);
    }

    public function addEntryAction(): Action
    {
        return Action::make('addEntry')
            ->slideOver()
            ->label('Add pipeline task')
            ->model(PipelineEntry::class)
            ->authorize(fn (): bool => auth()->user()->can('update', $this->pipeline))
            ->schema(PipelineEntryForm::components($this->pipeline, isStageVisible: false))
            ->action(function (array $data, array $arguments, Action $action) {
                $stage = $this->pipeline->stages()->whereKey($arguments['stage'] ?? null)->first();

                if (! $stage) {
                    Notification::make()
                        ->danger()
                        ->title('Pipeline task could not be added')
                        ->body('The selected stage does not belong to this pipeline.')
                        ->send();

                    $action->halt();

                    return;
                }

                $dataArray = [
                    'name' => $data['name'],
                    'pipeline_stage_id' => $stage->getKey(),
                    'description' => $data['description'] ?? null,
                    'start_date' => $data['start_date'] ?? null,
                    'due' => $data['due'] ?? null,
                    'assigned_to_type' => $data['assigned_to_type'] ?? null,
                    'assigned_to_id' => $data['assigned_to_id'] ?? null,
                    'project_milestone_id' => $data['project_milestone_id'] ?? null,
                    'is_visible_to_guests' => $data['is_visible_to_guests'] ?? true,
                ];

                $entry = new PipelineEntry($dataArray);

                $entry->saveOrFail();
                $entry->assets()->sync($data['assets'] ?? []);
                $entry->serviceRequests()->sync($data['serviceRequests'] ?? []);

                Notification::make()
                    ->success()
                    ->title('Pipeline Task Added!')
                    ->send();
            });
    }

    public function render(): View
    {
        $pipelineEntries = $this->getPipelineEntries();

        return view('project::livewire.pipeline-entry-kanban', [
            'pipelineEntries' => $pipelineEntries,
            'stages' => $this->getStages(),
            'stageCounts' => $pipelineEntries->map->count(),
        ]);
    }

    public function viewPipelineEntryAction(): ViewAction
    {
        return ViewPipelineEntryAction::make('viewPipelineEntry')
            ->authorize(fn (): bool => auth()->user()->can('view', $this->pipeline))
            ->record(fn (array $arguments): PipelineEntry => $this->resolvePipelineEntry($arguments['entry'] ?? null));
    }

    public function editPipelineEntryAction(): EditAction
    {
        return EditPipelineEntryAction::make($this->pipeline, 'editPipelineEntry')
            ->authorize(fn (): bool => auth()->user()->can('update', $this->pipeline))
            ->record(fn (array $arguments): PipelineEntry => $this->resolvePipelineEntry($arguments['entry'] ?? null));
    }

    public function removePipelineEntryAction(): Action
    {
        return Action::make('removePipelineEntry')
            ->label('Remove')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Remove Pipeline Task')
            ->authorize(fn (): bool => auth()->user()->can('update', $this->pipeline))
            ->action(function (array $arguments): void {
                $this->resolvePipelineEntry($arguments['entry'] ?? null)->delete();

                Notification::make()
                    ->success()
                    ->title('Pipeline task removed successfully')
                    ->send();
            });
    }

    protected function resolvePipelineEntry(mixed $entryId): PipelineEntry
    {
        return $this->pipeline->entries()
            ->whereKey($entryId)
            ->firstOrFail();
    }
}
