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

namespace AidingApp\Project\Livewire;

use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PipelineEntryKanban extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Pipeline $pipeline;

    public ?PipelineEntry $currentPipelineEntry = null;

    public function mount(Pipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @return Collection<string, Collection<int, PipelineEntry>>
     */
    public function getPipelineEntries(): Collection
    {
        /**
         * @var Collection<int, PipelineEntry> $entries
         */
        $entries = $this->pipeline->entries()->get();

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
        return PipelineStage::whereHas('pipeline', function (Builder $query) {
            return $query->where('id', $this->pipeline->getKey());
        })
            ->pluck('name', 'id');
    }

    public function moveEntry(Pipeline $pipeline, string $entryId, string $fromStage = '', string $toStage = ''): JsonResponse
    {
        try {
            PipelineEntry::where('pipeline_stage_id', $fromStage)
                ->where('id', $entryId)
                ->update([
                    'pipeline_stage_id' => $toStage,
                ]);
        } catch (Exception $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Pipline could not be moved. Something went wrong, if this continues please contact support.',
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
            ->make('Add pipeline entry')
            ->model(PipelineEntry::class)
            ->form([
                Grid::make()->schema([
                    TextInput::make('name')
                        ->maxLength(255)
                        ->required()
                        ->string(),
                ]),
                MorphToSelect::make('organizable')
                    ->types([
                        Type::make(Contact::class)
                            ->label('Contact')
                            ->titleAttribute('full_name')
                            ->modifyOptionsQueryUsing(fn (Builder $query) => $query->limit(50)),
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (array $data, array $arguments) {
                $entry = new PipelineEntry([
                    'name' => $data['name'],
                    'organizable_type' => $data['organizable_type'],
                    'organizable_id' => $data['organizable_id'],
                    'pipeline_stage_id' => $arguments['stage'],
                ]);

                $entry->saveOrFail();

                Notification::make()
                    ->success()
                    ->title('Entry Added!')
                    ->send();
            });
    }

    public function movedEntry(Pipeline $pipeline, string $entryId, string $fromStage = '', string $toStage = ''): JsonResponse
    {
        try {
            PipelineEntry::where('pipeline_stage_id', $fromStage)
                ->where('id', $entryId)
                ->update([
                    'pipeline_stage_id' => $toStage,
                ]);
        } catch (Exception $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Pipline could not be moved. Something went wrong, if this continues please contact support.',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pipeline stage updated successfully.',
        ], ResponseAlias::HTTP_OK);
    }

    public function render(): View
    {
        return view('project::livewire.pipeline-entry-kanban', [
            'pipelineEntries' => $this->getPipelineEntries(),
            'stages' => $this->getStages(),
        ]);
    }

    public function viewPipelineEntry(PipelineEntry $pipelineEntry): void
    {
        $this->redirect(PipelineResource::getUrl('view-pipeline-entry', [
            'record' => $this->pipeline->getKey(),
            'pipelineEntry' => $pipelineEntry->getKey(),
            'from' => 'kanban',
        ]));
    }
}
