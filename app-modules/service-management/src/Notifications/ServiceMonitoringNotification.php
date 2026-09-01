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

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\Scopes\ServiceMonitoringTargetVisibilityScope;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notification as BaseNotification;
use InvalidArgumentException;

class ServiceMonitoringNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public HistoricalServiceMonitoring $historicalServiceMonitoring, public string $channel) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $this->loadServiceMonitoringTarget();

        if (! $this->notifiableCanViewTarget($notifiable)) {
            return [];
        }

        return match ($this->channel) {
            DatabaseChannel::class => ['database'],
            MailChannel::class => ['mail'],
            'both' => ['database', 'mail'],
            default => throw new InvalidArgumentException(
                "Unsupported notification channel: {$this->channel}"
            ),
        };
    }

    public function toMail(User $notifiable): MailMessage
    {
        $this->loadServiceMonitoringTarget();

        return MailMessage::make()
            ->subject('Aiding App Service Monitoring Alert for ' . $this->historicalServiceMonitoring->serviceMonitoringTarget->name)
            ->markdown(
                'service-management::mail.service-monitoring-mail',
                [
                    'historicalServiceMonitoring' => $this->historicalServiceMonitoring,
                    'user' => $notifiable,
                ]
            );
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $this->loadServiceMonitoringTarget();
        $target = $this->historicalServiceMonitoring->serviceMonitoringTarget;
        $keywordMatchFailures = $this->historicalServiceMonitoring->keyword_match_failures;

        return Notification::make()
            ->danger()
            ->title((string) str("The last service monitoring check for [<ins>{$target->name}</ins>](" . ServiceMonitoringResource::getUrl('view', ['record' => $target]) . ') has failed.')->markdown())
            ->body(filled($keywordMatchFailures) ? collect($keywordMatchFailures)->map(fn (string $failure): string => "{$failure}")->implode("\n") : null)
            ->getDatabaseMessage();
    }

    // Notifications are queued and run without an authenticated user, so the confidentiality scope must be bypassed to load the target
    private function loadServiceMonitoringTarget(): void
    {
        if (filled($this->historicalServiceMonitoring->serviceMonitoringTarget)) {
            return;
        }

        $this->historicalServiceMonitoring->load([
            'serviceMonitoringTarget' => fn (BelongsTo $query) => $query->withoutGlobalScope(ServiceMonitoringTargetVisibilityScope::class),
        ]);
    }

    // The scope is bypassed above, so confidential targets must be re-checked against this specific notifiable
    private function notifiableCanViewTarget(object $notifiable): bool
    {
        $target = $this->historicalServiceMonitoring->serviceMonitoringTarget;

        if (! $target?->is_confidential) {
            return true;
        }

        return ServiceMonitoringTarget::query()
            ->withoutGlobalScope(ServiceMonitoringTargetVisibilityScope::class)
            ->whereKey($target->getKey())
            ->tap(fn (Builder $query) => (new ServiceMonitoringTargetVisibilityScope())->constrainFor(
                $query,
                $notifiable instanceof Authenticatable ? $notifiable : null,
            ))
            ->exists();
    }
}
