<?php

namespace AidingApp\Engagement\Jobs;

use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Notifications\EngagementNotification;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;

class DeliverEngagements implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function handle(): void
    {
        Engagement::query()
            ->where(fn (Builder $query) => $query
                ->whereNull('scheduled_at')
                ->orWhere('scheduled_at', '<=', now()))
            ->whereNull('dispatched_at')
            ->with('recipient')
            ->eachById(
                fn (Engagement $engagement) => DB::transaction(function () use ($engagement) {
                    $updatedEngagementsCount = Engagement::query()
                        ->whereNull('dispatched_at')
                        ->whereKey($engagement)
                        ->update(['dispatched_at' => now()]);

                    if (! $updatedEngagementsCount) {
                        return;
                    }

                    $engagement->recipient->notify(new EngagementNotification($engagement));
                }),
                250,
            );
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping(Tenant::current()->id))->dontRelease()->expireAfter(180)];
    }
}
