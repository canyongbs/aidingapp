<?php

namespace App\Models\Concerns;

use App\Enums\PresenceStatus;
use App\Settings\PresenceSettings;
use Carbon\Carbon;

trait HasPresence
{
    public function presenceStatus(): PresenceStatus
    {
        $lastActivityAt = $this->lastActivityAt();

        if (! $lastActivityAt) {
            return PresenceStatus::Offline;
        }

        $settings = app(PresenceSettings::class);
        $minutesAgo = $lastActivityAt->diffInMinutes(now());

        if ($minutesAgo < $settings->active_threshold) {
            return PresenceStatus::Active;
        }

        if ($minutesAgo < $settings->idle_threshold) {
            return PresenceStatus::Idle;
        }

        if ($minutesAgo < $settings->inactive_threshold) {
            return PresenceStatus::Inactive;
        }

        return PresenceStatus::Offline;
    }

    abstract protected function lastActivityAt(): ?Carbon;
}
