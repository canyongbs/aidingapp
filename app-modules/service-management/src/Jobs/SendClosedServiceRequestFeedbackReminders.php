<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Notifications\SendClosedServiceFeedbackNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendClosedServiceRequestFeedbackReminders implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $serviceRequests = ServiceRequest::query()
            ->whereHas('priority.type', function (Builder $query) {
                $query->where('is_reminders_enabled', true);
            })
            ->whereHas(
                'status',
                fn (Builder $query) => $query->where('classification', SystemServiceRequestClassification::Closed)
            )
            ->whereNull('reminder_sent_at')
            ->whereNotNull('survey_sent_at')
            ->where('survey_sent_at', '<=', now()->subHours(48))
            ->get();

        foreach ($serviceRequests as $serviceRequest) {
            $recipient = $serviceRequest->respondent ?? null;

            if (! $recipient) {
                continue;
            }

            $emailTemplate = $serviceRequest->priority?->type?->templates()
                ->first();

            $recipient->notify(
                (new SendClosedServiceFeedbackNotification($serviceRequest, $emailTemplate))
                    ->asReminder()
            );

            $serviceRequest->updateQuietly([
                'reminder_sent_at' => now(),
            ]);
        }
    }
}
