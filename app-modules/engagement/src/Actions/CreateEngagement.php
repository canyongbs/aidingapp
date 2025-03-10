<?php

namespace AidingApp\Engagement\Actions;

use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Notifications\EngagementNotification;
use Illuminate\Support\Facades\DB;

class CreateEngagement
{
    public function execute(EngagementCreationData $data): Engagement
    {
        $engagement = new Engagement();
        $engagement->user()->associate($data->user);
        $engagement->recipient()->associate($data->recipient);
        $engagement->channel = $data->channel;
        $engagement->subject = $data->subject;
        $engagement->scheduled_at = $data->scheduledAt;

        if (! $engagement->scheduled_at) {
            $engagement->dispatched_at = now();
        }

        DB::transaction(function () use ($data, $engagement) {
            $engagement->save();

            [$engagement->body] = tiptap_converter()->saveImages(
                $data->body,
                disk: 's3-public',
                record: $engagement,
                recordAttribute: 'body',
                newImages: $data->temporaryBodyImages,
            );
            $engagement->save();

            if (! $engagement->scheduled_at) {
                $engagement->recipient->notify(new EngagementNotification($engagement));
            }
        });

        return $engagement;
    }
}
