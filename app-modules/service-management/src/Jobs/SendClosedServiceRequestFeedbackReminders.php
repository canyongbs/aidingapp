<?php

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\Notification\Notifications\Messages\MailMessage as MessagesMailMessage;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendClosedServiceRequestFeedbackReminders implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        Log::debug('job is dispatched...');
        $cutoffTime = now()->subHours(48);

        $serviceRequests = ServiceRequest::query()
            ->where('is_reminders_enabled', true)
            ->whereNull('reminder_sent_at')
            ->whereNotNull('survey_sent_at')
            ->where('survey_sent_at', '<=', $cutoffTime)
            ->whereHas('status', function (Builder $query): void {
                $query->where(
                    'classification',
                    SystemServiceRequestClassification::Closed
                );
            })
            ->get();
        Log::debug($serviceRequests);

        foreach ($serviceRequests as $serviceRequest) {
            $recipient = $serviceRequest->contact ?? null;

            if (! $recipient) {
                continue;
            }

            $emailTemplate = $serviceRequest->emailTemplate ?? null;

            Notification::route('mail', $recipient->email)
                ->notify(new class ($serviceRequest, $emailTemplate) extends SendClosedServiceFeedbackNotification {
                    public function toMail(object $notifiable): MessagesMailMessage
                    {
                        $mail = parent::toMail($notifiable);
                        $mail->subject('Reminder: ' . $mail->subject);

                        return $mail;
                    }
                });

            $serviceRequest->updateQuietly([
                'reminder_sent_at' => now(),
            ]);
        }
    }
}
