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

namespace AidingApp\ServiceManagement\Rules;

use Illuminate\Support\Collection;

/**
 * A confidential service monitor only notifies recipients who are allowed to see it, so a
 * recipient without confidential access would silently never be alerted. This rejects that
 * combination at the form instead of letting the outage alert disappear at delivery time.
 */
class ServiceMonitorNotificationRecipientsMustHaveConfidentialAccess extends RecipientsMustHaveConfidentialAccess
{
    /**
     * @param list<string> $notifiedUserIds
     * @param list<string> $notifiedDepartmentIds
     * @param list<string> $confidentialUserIds
     * @param list<string> $confidentialDepartmentIds
     */
    public function __construct(
        array $notifiedUserIds,
        array $notifiedDepartmentIds,
        array $confidentialUserIds,
        array $confidentialDepartmentIds,
        ?string $creatorId,
    ) {
        parent::__construct($notifiedUserIds, $notifiedDepartmentIds, $confidentialUserIds, $confidentialDepartmentIds, $creatorId);
    }

    /**
     * @param Collection<int, string> $unreachable
     */
    protected function failureMessage(Collection $unreachable): string
    {
        return 'These notification recipients would not be able to see this service monitor, so they would never be alerted: '
            . $unreachable->join(', ', ' and ')
            . '. Grant them confidential access below, or remove them from the notification settings.';
    }
}
