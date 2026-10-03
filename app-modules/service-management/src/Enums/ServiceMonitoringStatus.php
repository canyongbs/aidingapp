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

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceMonitoringStatus: string implements HasLabel
{
    case Operational = 'operational';

    case Degraded = 'degraded';

    case Outage = 'outage';

    case Unknown = 'unknown';

    public function getLabel(): string
    {
        return match ($this) {
            self::Operational => 'Operational',
            self::Degraded => 'Degraded',
            self::Outage => 'Outage',
            self::Unknown => 'No data',
        };
    }

    /**
     * The status of a group of checks, such as those in one bar of a status history chart.
     */
    public static function fromCheckCounts(int $checksCount, int $successfulChecksCount): self
    {
        return match (true) {
            $checksCount === 0 => self::Unknown,
            $successfulChecksCount === $checksCount => self::Operational,
            $successfulChecksCount === 0 => self::Outage,
            default => self::Degraded,
        };
    }

    /**
     * The position of the status when sorting in ascending order, from no data through to the most severe.
     */
    public function getSortRank(): int
    {
        return match ($this) {
            self::Unknown => 0,
            self::Operational => 1,
            self::Degraded => 2,
            self::Outage => 3,
        };
    }
}
