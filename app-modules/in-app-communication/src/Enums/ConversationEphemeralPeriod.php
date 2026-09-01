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

namespace AidingApp\InAppCommunication\Enums;

use Carbon\CarbonInterface;
use Filament\Support\Contracts\HasLabel;

enum ConversationEphemeralPeriod: string implements HasLabel
{
    case OneMinute = '1_minute';

    case FiveMinutes = '5_minutes';

    case FifteenMinutes = '15_minutes';

    case OneHour = '1_hour';

    case TwentyFourHours = '24_hours';

    case SevenDays = '7_days';

    case FourteenDays = '14_days';

    case OneMonth = '1_month';

    case ThreeMonths = '3_months';

    case SixMonths = '6_months';

    case OneYear = '1_year';

    public function getLabel(): string
    {
        return match ($this) {
            self::OneMinute => '1 Minute',
            self::FiveMinutes => '5 Minutes',
            self::FifteenMinutes => '15 Minutes',
            self::OneHour => '1 Hour',
            self::TwentyFourHours => '24 Hours',
            self::SevenDays => '7 Days',
            self::FourteenDays => '14 Days',
            self::OneMonth => '1 Month',
            self::ThreeMonths => '3 Months',
            self::SixMonths => '6 Months',
            self::OneYear => '1 Year',
        };
    }

    public function subtractFrom(CarbonInterface $date): CarbonInterface
    {
        return match ($this) {
            self::OneMinute => $date->copy()->subMinute(),
            self::FiveMinutes => $date->copy()->subMinutes(5),
            self::FifteenMinutes => $date->copy()->subMinutes(15),
            self::OneHour => $date->copy()->subHour(),
            self::TwentyFourHours => $date->copy()->subHours(24),
            self::SevenDays => $date->copy()->subDays(7),
            self::FourteenDays => $date->copy()->subDays(14),
            self::OneMonth => $date->copy()->subMonthNoOverflow(),
            self::ThreeMonths => $date->copy()->subMonthsNoOverflow(3),
            self::SixMonths => $date->copy()->subMonthsNoOverflow(6),
            self::OneYear => $date->copy()->subYearNoOverflow(),
        };
    }
}
