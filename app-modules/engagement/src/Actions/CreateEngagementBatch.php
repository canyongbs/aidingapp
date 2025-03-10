<?php

namespace AidingApp\Engagement\Actions;

use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Jobs\CreateBatchedEngagement;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Engagement\Notifications\EngagementBatchFinishedNotification;
use AidingApp\Engagement\Notifications\EngagementBatchStartedNotification;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateEngagementBatch
{
    public function execute(EngagementCreationData $data): void
    {
        $engagementBatch = new EngagementBatch();
        $engagementBatch->user()->associate($data->user);
        $engagementBatch->channel = $data->channel;
        $engagementBatch->subject = $data->subject;
        $engagementBatch->scheduled_at = $data->scheduledAt;
        $engagementBatch->total_engagements = $data->recipient->count();
        $engagementBatch->processed_engagements = 0;
        $engagementBatch->successful_engagements = 0;

        DB::transaction(function () use ($engagementBatch, $data) {
            $engagementBatch->save();

            [$engagementBatch->body] = tiptap_converter()->saveImages(
                $data->body,
                disk: 's3-public',
                record: $engagementBatch,
                recordAttribute: 'body',
                newImages: $data->temporaryBodyImages,
            );

            $engagementBatch->save();
        });

        try {
            $batch = Bus::batch([
                ...blank($data->scheduledAt) ? [fn () => $engagementBatch->user->notify(new EngagementBatchStartedNotification($engagementBatch))] : [],
                ...$data->recipient
                    ->map(fn (CanBeNotified $recipient): CreateBatchedEngagement => new CreateBatchedEngagement($engagementBatch, $recipient))
                    ->all(),
            ])
                ->name("Bulk Engagement {$engagementBatch->getKey()}")
                ->finally(function () use ($engagementBatch) {
                    if ($engagementBatch->scheduled_at) {
                        return;
                    }

                    $engagementBatch->refresh();

                    $engagementBatch->user->notify(new EngagementBatchFinishedNotification($engagementBatch));
                })
                ->allowFailures()
                ->dispatch();

            $engagementBatch->identifier = $batch->id;
            $engagementBatch->save();
        } catch (Throwable $exception) {
            $engagementBatch->delete();

            throw $exception;
        }
    }
}
