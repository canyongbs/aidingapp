<?php

namespace AidingApp\Engagement\Jobs;

use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Engagement\Notifications\EngagementNotification;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateBatchedEngagement implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public EngagementBatch $engagementBatch,
        public CanBeNotified $recipient,
    ) {}

    public function handle(): void
    {
        $engagement = new Engagement();
        $engagement->engagementBatch()->associate($this->engagementBatch);
        $engagement->user()->associate($this->engagementBatch->user);
        $engagement->recipient()->associate($this->recipient);
        $engagement->channel = $this->engagementBatch->channel;
        $engagement->subject = $this->engagementBatch->subject;
        $engagement->body = $this->engagementBatch->body;
        $engagement->scheduled_at = $this->engagementBatch->scheduled_at;

        if (! $engagement->scheduled_at) {
            $engagement->dispatched_at = now();
        }

        DB::transaction(function () use ($engagement) {
            $engagement->save();

            if (! $engagement->scheduled_at) {
                $engagement->recipient->notifyNow(new EngagementNotification($engagement));
            }
        });
    }
}
