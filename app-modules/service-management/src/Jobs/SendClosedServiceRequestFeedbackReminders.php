<?php

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
                $query->where('has_feedback_reminder', true);
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
