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

use AidingApp\InAppCommunication\Enums\ConversationEphemeralPeriod;
use Illuminate\Support\Carbon;

it('has a human readable label', function (ConversationEphemeralPeriod $period, string $label) {
    expect($period->getLabel())->toBe($label);
})->with([
    'one minute' => [ConversationEphemeralPeriod::OneMinute, '1 Minute'],
    'five minutes' => [ConversationEphemeralPeriod::FiveMinutes, '5 Minutes'],
    'fifteen minutes' => [ConversationEphemeralPeriod::FifteenMinutes, '15 Minutes'],
    'one hour' => [ConversationEphemeralPeriod::OneHour, '1 Hour'],
    'twenty four hours' => [ConversationEphemeralPeriod::TwentyFourHours, '24 Hours'],
    'seven days' => [ConversationEphemeralPeriod::SevenDays, '7 Days'],
    'fourteen days' => [ConversationEphemeralPeriod::FourteenDays, '14 Days'],
    'one month' => [ConversationEphemeralPeriod::OneMonth, '1 Month'],
    'three months' => [ConversationEphemeralPeriod::ThreeMonths, '3 Months'],
    'six months' => [ConversationEphemeralPeriod::SixMonths, '6 Months'],
    'one year' => [ConversationEphemeralPeriod::OneYear, '1 Year'],
]);

it('resolves the cutoff for a given date', function (ConversationEphemeralPeriod $period, string $expected) {
    $date = Carbon::parse('2026-08-24 12:00:00');

    expect($period->subtractFrom($date)->toDateTimeString())->toBe($expected);
})->with([
    'one minute' => [ConversationEphemeralPeriod::OneMinute, '2026-08-24 11:59:00'],
    'five minutes' => [ConversationEphemeralPeriod::FiveMinutes, '2026-08-24 11:55:00'],
    'fifteen minutes' => [ConversationEphemeralPeriod::FifteenMinutes, '2026-08-24 11:45:00'],
    'one hour' => [ConversationEphemeralPeriod::OneHour, '2026-08-24 11:00:00'],
    'twenty four hours' => [ConversationEphemeralPeriod::TwentyFourHours, '2026-08-23 12:00:00'],
    'seven days' => [ConversationEphemeralPeriod::SevenDays, '2026-08-17 12:00:00'],
    'fourteen days' => [ConversationEphemeralPeriod::FourteenDays, '2026-08-10 12:00:00'],
    'one month' => [ConversationEphemeralPeriod::OneMonth, '2026-07-24 12:00:00'],
    'three months' => [ConversationEphemeralPeriod::ThreeMonths, '2026-05-24 12:00:00'],
    'six months' => [ConversationEphemeralPeriod::SixMonths, '2026-02-24 12:00:00'],
    'one year' => [ConversationEphemeralPeriod::OneYear, '2025-08-24 12:00:00'],
]);

it('does not mutate the date it is given', function () {
    $date = Carbon::parse('2026-08-24 12:00:00');

    ConversationEphemeralPeriod::OneYear->subtractFrom($date);

    expect($date->toDateTimeString())->toBe('2026-08-24 12:00:00');
});

it('does not overflow calendar period cutoffs', function (ConversationEphemeralPeriod $period, string $date, string $expected) {
    expect($period->subtractFrom(Carbon::parse($date))->toDateTimeString())->toBe($expected);
})->with([
    'one month from the end of March' => [ConversationEphemeralPeriod::OneMonth, '2026-03-31 12:00:00', '2026-02-28 12:00:00'],
    'three months across February' => [ConversationEphemeralPeriod::ThreeMonths, '2026-05-31 12:00:00', '2026-02-28 12:00:00'],
    'six months across February' => [ConversationEphemeralPeriod::SixMonths, '2026-08-31 12:00:00', '2026-02-28 12:00:00'],
    'one year from leap day' => [ConversationEphemeralPeriod::OneYear, '2028-02-29 12:00:00', '2027-02-28 12:00:00'],
]);
