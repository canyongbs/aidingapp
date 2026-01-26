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

namespace App\Models\Concerns;

use App\Features\ServiceRequestFormAndTypeArchivingFeature;
use App\Models\Scopes\ArchivingScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Events\QueuedClosure;

/**
 * @method static Builder<static> withArchived()
 * @method static Builder<static> onlyArchived()
 * @method static Builder<static> withoutArchived()
 */
trait CanBeArchived
{
    public static function bootCanBeArchived(): void
    {
        if (! ServiceRequestFormAndTypeArchivingFeature::active()) {
            return;
        }

        static::addGlobalScope(new ArchivingScope());
    }

    public function initializeCanBeArchived(): void
    {
        if (! isset($this->casts[$this->getArchivedAtColumn()])) {
            $this->casts[$this->getArchivedAtColumn()] = 'datetime';
        }
    }

    public function archive(): bool
    {
        if ($this->fireModelEvent('archiving') === false) {
            return false;
        }

        /** @var Builder<static> $query */
        $query = $this->newModelQuery();

        $query = $this->setKeysForSaveQuery($query);

        $time = $this->freshTimestamp();

        $columns = [$this->getArchivedAtColumn() => $this->fromDateTime($time)];

        $this->{$this->getArchivedAtColumn()} = $time;

        if ($this->usesTimestamps() && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('archived', false);

        return true;
    }

    public function archiveQuietly(): bool
    {
        return static::withoutEvents(fn () => $this->archive());
    }

    public function unarchive(): bool
    {
        if ($this->fireModelEvent('unarchiving') === false) {
            return false;
        }

        $this->{$this->getArchivedAtColumn()} = null;

        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('unarchived', false);

        return $result;
    }

    public function unarchiveQuietly(): bool
    {
        return static::withoutEvents(fn () => $this->unarchive());
    }

    public function isArchived(): bool
    {
        return ! is_null($this->{$this->getArchivedAtColumn()});
    }

    /**
     * @param  QueuedClosure|callable|class-string  $callback
     */
    public static function archiving(QueuedClosure | callable | string $callback): void
    {
        static::registerModelEvent('archiving', $callback);
    }

    /**
     * @param  QueuedClosure|callable|class-string  $callback
     */
    public static function archived(QueuedClosure | callable | string $callback): void
    {
        static::registerModelEvent('archived', $callback);
    }

    /**
     * @param  QueuedClosure|callable|class-string  $callback
     */
    public static function unarchiving(QueuedClosure | callable | string $callback): void
    {
        static::registerModelEvent('unarchiving', $callback);
    }

    /**
     * @param  QueuedClosure|callable|class-string  $callback
     */
    public static function unarchived(QueuedClosure | callable | string $callback): void
    {
        static::registerModelEvent('unarchived', $callback);
    }

    public function getArchivedAtColumn(): string
    {
        return 'archived_at';
    }

    public function getQualifiedArchivedAtColumn(): string
    {
        return $this->qualifyColumn($this->getArchivedAtColumn());
    }
}
