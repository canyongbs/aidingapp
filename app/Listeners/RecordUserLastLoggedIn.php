<?php

namespace App\Listeners;

use App\Features\LastLoggedInFeature;
use App\Models\User;
use Illuminate\Auth\Events\Login;

class RecordUserLastLoggedIn
{
    public function handle(Login $event): void
    {
        if (! LastLoggedInFeature::active()) {
            return;
        }

        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        User::withoutTimestamps(fn () => $user->touchQuietly('last_logged_in_at'));
    }
}
