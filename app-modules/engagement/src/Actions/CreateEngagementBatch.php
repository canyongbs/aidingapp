<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Engagement\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\DataTransferObjects\EngagementBatchCreationData;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Engagement\Models\EngagementDeliverable;
use AidingApp\Engagement\Notifications\EngagementBatchFinishedNotification;
use AidingApp\Engagement\Notifications\EngagementBatchStartedNotification;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class CreateEngagementBatch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public EngagementBatchCreationData $data
    ) {}

    public function handle(): void
    {
        $engagementBatch = EngagementBatch::create([
            'user_id' => $this->data->user->id,
        ]);

        [$body] = tiptap_converter()->saveImages(
            $this->data->body,
            disk: 's3-public',
            record: $engagementBatch,
            recordAttribute: 'body',
            newImages: $this->data->temporaryBodyImages,
        );

        $this->data->records->each(function (Contact $record) use ($body, $engagementBatch) {
            /** @var Engagement $engagement */
            $engagement = $engagementBatch->engagements()->create([
                'user_id' => $engagementBatch->user_id,
                'recipient_id' => $record->getKey(),
                'recipient_type' => $record->getMorphClass(),
                'body' => $body,
                'subject' => $this->data->subject,
                'scheduled' => false,
            ]);

            $createEngagementDeliverable = resolve(CreateEngagementDeliverable::class);

            $createEngagementDeliverable($engagement, $this->data->deliveryMethod);
        });

        $deliverables = $engagementBatch->engagements->map(function (Engagement $engagement) {
            return $engagement->deliverable;
        });

        $deliverableJobs = $deliverables->flatten()->map(function (EngagementDeliverable $deliverable) {
            return $deliverable->driver()->jobForDelivery();
        });

        $engagementBatch->user->notify(new EngagementBatchStartedNotification($engagementBatch, $deliverableJobs->count()));

        Bus::batch($deliverableJobs)
            ->name("Process Bulk Engagement {$engagementBatch->id}")
            ->finally(function (Batch $batchQueue) use ($engagementBatch) {
                $engagementBatch->user->notify(new EngagementBatchFinishedNotification($engagementBatch, $batchQueue->totalJobs, $batchQueue->failedJobs));
            })
            ->allowFailures()
            ->dispatch();
    }
}
