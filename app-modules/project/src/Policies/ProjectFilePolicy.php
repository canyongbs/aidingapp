<?php

namespace AidingApp\Project\Policies;

use AidingApp\Project\Models\ProjectFile;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ProjectFilePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.view-any',
            denyResponse: 'You do not have permission to view project files.'
        );
    }

    public function view(Authenticatable $authenticatable, ProjectFile $projectFile): Response
    {
        if ($authenticatable->can('view', $projectFile->project)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view file.');
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.create',
            denyResponse: 'You do not have permission to create file.'
        );
    }

    public function update(Authenticatable $authenticatable, ProjectFile $projectFile): Response
    {
        if ($authenticatable->can('update', $projectFile->project)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update this file.');
    }

    public function delete(Authenticatable $authenticatable, ProjectFile $projectFile): Response
    {
        if ($authenticatable->can('delete', $projectFile->project)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to delete this file.');
    }
}
