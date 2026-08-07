<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
