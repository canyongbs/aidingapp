<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Project\Policies;

use AidingApp\Project\Models\Project;
use AidingApp\Team\Models\Team;
use App\Features\ProjectFeatureFlag;
use App\Features\ProjectManagersAuditorsFeature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;

class ProjectPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! ProjectFeatureFlag::active()) {
            return Response::deny('This feature is not active currently.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.view-any',
            denyResponse: 'You do not have permission to view projects.'
        );
    }

    public function view(Authenticatable $authenticatable, Project $project): Response
    {
        if (ProjectManagersAuditorsFeature::active() && ! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;
            /** @var Collection<int, Team> $managerTeams */
            $managerTeams = $project->managerTeams;
            /** @var Collection<int, Team> $managerUsers */
            $managerUsers = $project->managerUsers;
            /** @var Collection<int, Team> $auditorTeams */
            $auditorTeams = $project->auditorTeams;
            /** @var Collection<int, Team> $auditorUsers */
            $auditorUsers = $project->auditorUsers;

            if (
                ! $managerTeams->contains('id', $team?->getKey()) &&
                ! $auditorTeams->contains('id', $team?->getKey()) &&
                ! $managerUsers->contains('id', auth()->user()->getKey()) &&
                ! $auditorUsers->contains('id', auth()->user()->getKey()) &&
                ! $project->createdBy?->is(auth()->user())
            ) {
                return Response::deny("You don't have permission to view this project because you're not an auditor or manager or creator of this project.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: 'project.*.view',
            denyResponse: 'You do not have permission to view this project.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.create',
            denyResponse: 'You do not have permission to create projects.'
        );
    }

    public function update(Authenticatable $authenticatable, Project $project): Response
    {
        if (ProjectManagersAuditorsFeature::active() && ! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            /** @var Collection<int, Team> $managerTeams */
            $managerTeams = $project->managerTeams;
            /** @var Collection<int, Team> $managerUsers */
            $managerUsers = $project->managerUsers;
            /** @var Collection<int, Team> $auditorTeams */
            $auditorTeams = $project->auditorTeams;
            /** @var Collection<int, Team> $auditorUsers */
            $auditorUsers = $project->auditorUsers;

            if (
                ! $managerTeams->contains('id', $team?->getKey()) &&
                ! $auditorTeams->contains('id', $team?->getKey()) &&
                ! $managerUsers->contains('id', auth()->user()->getKey()) &&
                ! $auditorUsers->contains('id', auth()->user()->getKey()) &&
                ! $project->createdBy?->is(auth()->user())
            ) {
                return Response::deny("You don't have permission to update this project because you're not an auditor or manager or creator of this project.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: 'project.*.update',
            denyResponse: 'You do not have permission to update this project.'
        );
    }

    public function delete(Authenticatable $authenticatable, Project $project): Response
    {
        if (ProjectManagersAuditorsFeature::active() && ! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            /** @var Collection<int, Team> $managerTeams */
            $managerTeams = $project->managerTeams;
            /** @var Collection<int, Team> $managerUsers */
            $managerUsers = $project->managerUsers;
            /** @var Collection<int, Team> $auditorTeams */
            $auditorTeams = $project->auditorTeams;
            /** @var Collection<int, Team> $auditorUsers */
            $auditorUsers = $project->auditorUsers;

            if (
                ! $managerTeams->contains('id', $team?->getKey()) &&
                ! $auditorTeams->contains('id', $team?->getKey()) &&
                ! $managerUsers->contains('id', auth()->user()->getKey()) &&
                ! $auditorUsers->contains('id', auth()->user()->getKey()) &&
                ! $project->createdBy?->is(auth()->user())
            ) {
                return Response::deny("You don't have permission to delete this project because you're not an auditor or manager or creator of this project.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: 'project.*.delete',
            denyResponse: 'You do not have permission to delete this project.'
        );
    }

    public function restore(Authenticatable $authenticatable, Project $project): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.*.restore',
            denyResponse: 'You do not have permission to restore this project.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Project $project): Response
    {
        if (ProjectManagersAuditorsFeature::active() && ! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            /** @var Collection<int, Team> $managerTeams */
            $managerTeams = $project->managerTeams;
            /** @var Collection<int, Team> $managerUsers */
            $managerUsers = $project->managerUsers;
            /** @var Collection<int, Team> $auditorTeams */
            $auditorTeams = $project->auditorTeams;
            /** @var Collection<int, Team> $auditorUsers */
            $auditorUsers = $project->auditorUsers;

            if (
                ! $managerTeams->contains('id', $team?->getKey()) &&
                ! $auditorTeams->contains('id', $team?->getKey()) &&
                ! $managerUsers->contains('id', auth()->user()->getKey()) &&
                ! $auditorUsers->contains('id', auth()->user()->getKey()) &&
                ! $project->createdBy?->is(auth()->user())
            ) {
                return Response::deny("You don't have permission to permanently delete this project because you're not an auditor or manager or creator of this project.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: 'project.*.force-delete',
            denyResponse: 'You do not have permission to permanently delete this project.'
        );
    }
}
