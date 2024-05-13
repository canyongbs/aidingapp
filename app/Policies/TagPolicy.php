<?php

namespace App\Policies;

use App\Models\Tag;
use Laravel\Pennant\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class TagPolicy
{
    public function before(): ?bool
    {
        return Feature::inactive('tags') ? false : null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.view-any'],
            denyResponse: 'You do not have permission to view tags.'
        );
    }

    public function view(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.view', "tag.{$tag->id}.view"],
            denyResponse: 'You do not have permission to view this tag.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'tag.create',
            denyResponse: 'You do not have permission to create tags.'
        );
    }

    public function update(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.update', "tag.{$tag->id}.update"],
            denyResponse: 'You do not have permission to update this tag.'
        );
    }

    public function delete(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.delete', "tag.{$tag->id}.delete"],
            denyResponse: 'You do not have permission to delete this tag.'
        );
    }

    public function restore(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.restore', "tag.{$tag->id}.restore"],
            denyResponse: 'You do not have permission to restore this tag.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Tag $tag): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['tag.*.force-delete', "tag.{$tag->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this tag.'
        );
    }
}
