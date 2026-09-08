<?php

namespace AidingApp\Group\Policies;

use AidingApp\Group\Models\Group;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'group.view-any',
            denyResponse: 'You do not have permission to view groups.'
        );
    }

    public function view(Authenticatable $authenticatable, Group $group): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'group.*.view',
            denyResponse: 'You do not have permission to view this group.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'group.create',
            denyResponse: 'You do not have permission to create groups.'
        );
    }

    public function update(Authenticatable $authenticatable, Group $group): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'group.*.update',
            denyResponse: 'You do not have permission to update this group.'
        );
    }

    public function delete(Authenticatable $authenticatable, Group $group): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'group.*.delete',
            denyResponse: 'You do not have permission to delete this group.'
        );
    }

    public function deleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'group.*.delete',
            denyResponse: 'You do not have permission to delete any groups.'
        );
    }
}
