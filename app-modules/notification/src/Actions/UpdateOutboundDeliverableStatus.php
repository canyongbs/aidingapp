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

namespace AidingApp\Notification\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\DataTransferObjects\UpdateDeliveryStatusData;
use AidingApp\IntegrationTwilio\DataTransferObjects\TwilioStatusCallbackData;

class UpdateOutboundDeliverableStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public OutboundDeliverable $deliverable,
        public TwilioStatusCallbackData $data
    ) {}

    public function handle(): void
    {
        $data = UpdateDeliveryStatusData::from([
            'data' => $this->data,
        ]);

        $this->deliverable->driver()->updateDeliveryStatus($data);

        if ($this->deliverable->related) {
            if (method_exists($this->deliverable->related, 'driver')) {
                $this->deliverable->related->driver()->updateDeliveryStatus($data);
            }
        }
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->deliverable->id))
                ->releaseAfter(30)
                ->expireAfter(300),
        ];
    }
}
