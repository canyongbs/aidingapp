<?php

namespace App\Models\Concerns;

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
