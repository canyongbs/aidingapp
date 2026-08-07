<?php

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\Department\Models\Department;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringReportNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ServiceMonitoringReportNotifyJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ServiceMonitoringTarget $serviceMonitoringTarget) {}

    public function uniqueId(): string
    {
        return $this->serviceMonitoringTarget->getKey();
    }

    /**
     * Return the period for which this job should be unique for, half an hour, in seconds
     */
    public function uniqueFor(): int
    {
        return 30 * 60;
    }

    public function handle(): void
    {
        $recipientUsers = $this->serviceMonitoringTarget->reportUsers()->get();
        $recipientContacts = $this->serviceMonitoringTarget->reportContacts()->get();

        $this->serviceMonitoringTarget->reportDepartments()->each(function (Department $department) use (&$recipientUsers) {
            $users = $department->users()->get();
            $recipientUsers = $recipientUsers->merge($users)->unique('id');
        });

        $channel = match (true) {
            $this->serviceMonitoringTarget->is_reported_via_email && $this->serviceMonitoringTarget->is_reported_via_database => 'both',
            $this->serviceMonitoringTarget->is_reported_via_email => MailChannel::class,
            $this->serviceMonitoringTarget->is_reported_via_database => DatabaseChannel::class,
            default => null,
        };

        if (! $channel) {
            return;
        }

        Notification::send($recipientUsers, new ServiceMonitoringReportNotification($this->serviceMonitoringTarget, $channel));
        Notification::send($recipientContacts, new ServiceMonitoringReportNotification($this->serviceMonitoringTarget, $channel));
    }
}
