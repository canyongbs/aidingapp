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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use App\Settings\LicenseSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowPortalProjectController
{
    public function __invoke(Request $request, Project $portalProject): JsonResponse
    {
        $contact = auth('contact')->user();

        abort_unless(
            $contact instanceof Contact && resolve(LicenseSettings::class)->data?->addons->projectManagement,
            Response::HTTP_FORBIDDEN,
        );

        $pipelines = $portalProject->pipelines()
            ->withoutArchived()
            ->oldest()
            ->get(['id', 'name']);

        $requestedPipelineId = $request->string('pipeline')->toString();
        $selectedPipeline = filled($requestedPipelineId)
            ? $pipelines->firstWhere('id', $requestedPipelineId)
            : $pipelines->first();

        abort_if(filled($requestedPipelineId) && ! $selectedPipeline, Response::HTTP_NOT_FOUND);

        if (! $selectedPipeline) {
            return $this->response($portalProject, $pipelines, null, collect(), $this->emptyMeta());
        }

        $entries = $selectedPipeline->entries()
            ->withoutArchived()
            ->where('is_visible_to_guests', true)
            ->whereIn(
                'pipeline_stage_id',
                PipelineStage::query()
                    ->withoutArchived()
                    ->select('id'),
            )
            ->where(function (Builder $query): void {
                $query->whereNull('project_milestone_id')
                    ->orWhereIn(
                        'project_milestone_id',
                        ProjectMilestone::query()
                            ->withoutArchived()
                            ->select('id'),
                    );
            })
            ->with([
                'pipelineStage:id,pipeline_id,name,classification',
            ])
            ->orderByRaw('pipeline_entries.project_milestone_id IS NULL')
            ->orderBy(
                ProjectMilestone::query()
                    ->select('title')
                    ->whereColumn('project_milestones.id', 'pipeline_entries.project_milestone_id'),
            )
            ->oldest('pipeline_entries.created_at')
            ->paginate(50, [
                'pipeline_entries.id',
                'pipeline_entries.name',
                'pipeline_entries.pipeline_stage_id',
                'pipeline_entries.project_milestone_id',
                'pipeline_entries.start_date',
                'pipeline_entries.due',
            ]);

        $milestoneIds = $entries->getCollection()
            ->pluck('project_milestone_id')
            ->filter()
            ->unique()
            ->values();

        $milestones = ProjectMilestone::query()
            ->withoutArchived()
            ->whereKey($milestoneIds)
            ->withCount([
                'pipelineEntries as total_entries_count' => fn (Builder $query): Builder => $this->constrainMilestoneEntries($query, $selectedPipeline),
                'pipelineEntries as complete_entries_count' => fn (Builder $query): Builder => $this->constrainMilestoneEntries($query, $selectedPipeline)
                    ->whereHas(
                        'pipelineStage',
                        fn (Builder $query): Builder => $query->where('classification', PipelineStageClassification::Complete->value),
                    ),
            ])
            ->get(['id', 'title']);

        return $this->response(
            $portalProject,
            $pipelines,
            $selectedPipeline,
            $entries->getCollection(),
            [
                'current_page' => $entries->currentPage(),
                'last_page' => $entries->lastPage(),
                'from' => $entries->firstItem() ?? 0,
                'to' => $entries->lastItem() ?? 0,
                'total' => $entries->total(),
                'per_page' => $entries->perPage(),
            ],
            $milestones,
        );
    }

    /**
     * @param Collection<int, Pipeline> $pipelines
     * @param Collection<int, PipelineEntry> $entries
     * @param Collection<int, ProjectMilestone> $milestones
     * @param array{current_page: int, last_page: int, from: int, to: int, total: int, per_page: int} $meta
     */
    private function response(
        Project $project,
        Collection $pipelines,
        ?Pipeline $selectedPipeline,
        Collection $entries,
        array $meta,
        Collection $milestones = new Collection(),
    ): JsonResponse {
        return response()->json([
            'data' => [
                'id' => $project->getKey(),
                'name' => $project->name,
                'pipelines' => $pipelines->map(fn (Pipeline $pipeline): array => [
                    'id' => $pipeline->getKey(),
                    'name' => $pipeline->name,
                    ...($pipeline->is($selectedPipeline) ? [
                        'groups' => $this->pipelineGroups($entries, $milestones),
                    ] : []),
                ]),
            ],
            'meta' => $meta,
        ]);
    }

    /**
     * @param Builder<PipelineEntry> $query
     *
     * @return Builder<PipelineEntry>
     */
    private function constrainMilestoneEntries(Builder $query, Pipeline $pipeline): Builder
    {
        return $query
            ->withoutArchived()
            ->whereHas(
                'pipelineStage',
                fn (Builder $query): Builder => $query
                    ->withoutArchived()
                    ->whereBelongsTo($pipeline, 'pipeline'),
            );
    }

    /**
     * @return array{current_page: int, last_page: int, from: int, to: int, total: int, per_page: int}
     */
    private function emptyMeta(): array
    {
        return [
            'current_page' => 1,
            'last_page' => 1,
            'from' => 0,
            'to' => 0,
            'total' => 0,
            'per_page' => 50,
        ];
    }

    /**
     * @param Collection<int, PipelineEntry> $entries
     * @param Collection<int, ProjectMilestone> $milestones
     *
     * @return array<int, array<string, mixed>>
     */
    private function pipelineGroups(Collection $entries, Collection $milestones): array
    {
        $milestoneGroups = $milestones
            ->map(function (ProjectMilestone $milestone) use ($entries): array {
                $milestoneEntries = $entries->where('project_milestone_id', $milestone->getKey());
                $totalEntriesCount = (int) $milestone->getAttribute('total_entries_count');
                $completeEntriesCount = (int) $milestone->getAttribute('complete_entries_count');

                return [
                    'milestone_id' => $milestone->getKey(),
                    'milestone_title' => $milestone->title,
                    'progress_percentage' => $totalEntriesCount === 0
                        ? 0
                        : (int) round(($completeEntriesCount / $totalEntriesCount) * 100),
                    'entries' => $milestoneEntries
                        ->map($this->mapEntry(...))
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();

        $unassignedEntries = $entries
            ->whereNull('project_milestone_id');

        if ($unassignedEntries->isNotEmpty()) {
            $milestoneGroups[] = [
                'milestone_id' => null,
                'milestone_title' => 'No Associated Milestone',
                'progress_percentage' => null,
                'entries' => $unassignedEntries->map($this->mapEntry(...))->values()->all(),
            ];
        }

        return $milestoneGroups;
    }

    /**
     * @return array{id: string, name: string, stage: string, start_date: ?string, due: ?string}
     */
    private function mapEntry(PipelineEntry $entry): array
    {
        return [
            'id' => $entry->getKey(),
            'name' => $entry->name,
            'stage' => $entry->pipelineStage->name,
            'start_date' => $entry->start_date?->toDateString(),
            'due' => $entry->due?->toDateString(),
        ];
    }
}
