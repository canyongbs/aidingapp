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
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\Project\Models\Scopes\VisibleToPortalContact;
use App\Settings\LicenseSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ShowPortalProjectController
{
    public function __invoke(string $project): JsonResponse
    {
        $contact = auth('contact')->user();

        abort_unless(
            $contact instanceof Contact && resolve(LicenseSettings::class)->data?->addons->projectManagement,
            Response::HTTP_FORBIDDEN,
        );

        $project = Project::query()
            ->withoutArchived()
            ->tap(new VisibleToPortalContact($contact))
            ->whereKey($project)
            ->firstOrFail();

        $pipelines = $project->pipelines()
            ->withoutArchived()
            ->oldest()
            ->get(['id', 'name']);

        $milestones = $project->milestones()
            ->withoutArchived()
            ->orderBy('title')
            ->get(['id', 'title']);

        $entries = PipelineEntry::query()
            ->withoutArchived()
            ->whereHas(
                'pipelineStage',
                fn(Builder $query): Builder => $query
                    ->withoutArchived()
                    ->whereIn('pipeline_id', $pipelines->modelKeys()),
            )
            ->where(function (Builder $query): void {
                $query->whereNull('project_milestone_id')
                    ->orWhereHas(
                        'milestone',
                        fn(Builder $query): Builder => $query->withoutArchived(),
                    );
            })
            ->with([
                'pipelineStage:id,pipeline_id,name,classification',
                'milestone:id,title',
            ])
            ->oldest()
            ->get([
                'id',
                'name',
                'pipeline_stage_id',
                'project_milestone_id',
                'is_visible_to_guests',
                'start_date',
                'due',
            ]);

        return response()->json([
            'data' => [
                'id' => $project->getKey(),
                'name' => $project->name,
                'pipelines' => $pipelines->map(fn(Pipeline $pipeline): array => [
                    'id' => $pipeline->getKey(),
                    'name' => $pipeline->name,
                    'groups' => $this->pipelineGroups(
                        $entries->filter(
                            fn(PipelineEntry $entry): bool => $entry->pipelineStage->pipeline_id === $pipeline->getKey(),
                        ),
                        $milestones,
                    ),
                ]),
            ],
        ]);
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
                $totalEntries = $milestoneEntries->count();
                $completeEntries = $milestoneEntries->filter(
                    fn(PipelineEntry $entry): bool => $entry->pipelineStage->classification === PipelineStageClassification::Complete,
                )->count();

                return [
                    'milestone_id' => $milestone->getKey(),
                    'milestone_title' => $milestone->title,
                    'progress_percentage' => $totalEntries === 0
                        ? 0
                        : (int) round(($completeEntries / $totalEntries) * 100),
                    'entries' => $milestoneEntries
                        ->where('is_visible_to_guests', true)
                        ->map($this->mapEntry(...))
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();

        $unassignedEntries = $entries
            ->whereNull('project_milestone_id')
            ->where('is_visible_to_guests', true);

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
