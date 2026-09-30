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
import { resolveTimezone } from '../../Services/FormatDateTime.js';
import { formatUptimePercentage, getStatus } from './serviceMonitorStatuses.js';

export const DEFAULT_HISTORY_PERIOD = 'past_month';

/**
 * The periods the history chart can show, with how each bucket is labelled along the axis and in tooltips.
 */
export const HISTORY_PERIODS = {
    past_hour: {
        label: 'Past hour',
        heading: 'Uptime history (past hour)',
        description: 'Service availability for each minute of the last hour.',
        axisLabelEvery: 10,
        axisFormat: { hour: 'numeric', minute: '2-digit' },
        tooltipFormat: { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' },
    },
    past_day: {
        label: 'Past 24 hours',
        heading: 'Uptime history (past 24 hours)',
        description: 'Hourly service availability for the last 24 hours.',
        axisLabelEvery: 3,
        axisFormat: { hour: 'numeric' },
        tooltipFormat: { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' },
    },
    past_month: {
        label: 'Past 30 days',
        heading: 'Uptime history (past 30 days)',
        description: 'Daily service availability for the last 30 days.',
        axisLabelEvery: 3,
        axisFormat: { month: 'short', day: 'numeric' },
        tooltipFormat: { weekday: 'short', month: 'short', day: 'numeric' },
    },
};

export const HISTORY_PERIOD_OPTIONS = Object.entries(HISTORY_PERIODS).map(([value, { label }]) => ({ value, label }));

export function getHistoryPeriod(period) {
    return HISTORY_PERIODS[period] ?? HISTORY_PERIODS[DEFAULT_HISTORY_PERIOD];
}

export function formatBucketStart(startsAt, format) {
    return new Intl.DateTimeFormat('en-US', { ...format, timeZone: resolveTimezone() }).format(new Date(startsAt));
}

/**
 * A plain-language summary of a bucket, e.g. "Mon, Oct 12: Degraded, 99.65% uptime (1 of 288 checks failed)".
 */
export function describeBucket(bucket, period) {
    const when = formatBucketStart(bucket.starts_at, getHistoryPeriod(period).tooltipFormat);
    const status = getStatus(bucket.status).label;

    if (bucket.checks_count === 0) {
        return `${when}: ${status}`;
    }

    const failures =
        bucket.failed_checks_count === 0
            ? `${bucket.checks_count} ${bucket.checks_count === 1 ? 'check' : 'checks'} passed`
            : `${bucket.failed_checks_count} of ${bucket.checks_count} checks failed`;

    return `${when}: ${status}, ${formatUptimePercentage(bucket.uptime_percentage)} uptime (${failures})`;
}
