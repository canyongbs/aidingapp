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
import { useTimezoneStore } from '../Stores/timezone.js';

/**
 * Resolve the timezone to display dates in: the managed-contact `display_timezone`
 * when available, otherwise the viewer's browser timezone (detected on load). The
 * live `Intl` lookup is only a safety net for the rare render before detection ran.
 */
function resolveTimezone() {
    const store = useTimezoneStore();

    return store.displayTimezone ?? store.browserTimezone ?? Intl.DateTimeFormat().resolvedOptions().timeZone;
}

/**
 * portal's established style, e.g. "Jul 22, 2026 1:48 pm (EDT)".
 *
 * @param {string|Date|null} value
 * @param {object}  [options]
 * @param {boolean} [options.dateOnly]
 * @param {boolean} [options.timeOnly]
 * @param {boolean} [options.utc]
 * @param {boolean} [options.withZone=true]
 * @returns {string|null}
 */
export default function formatDateTime(value, { dateOnly = false, timeOnly = false, utc = false, withZone = true } = {}) {
    if (!value) {
        return null;
    }

    const date = value instanceof Date ? value : new Date(value);

    if (isNaN(date.getTime())) {
        return null;
    }

    const timeZone = utc ? 'UTC' : resolveTimezone();

    const formatOptions = { timeZone };

    if (!timeOnly) {
        formatOptions.month = 'short';
        formatOptions.day = 'numeric';
        formatOptions.year = 'numeric';
    }

    if (!dateOnly) {
        formatOptions.hour = 'numeric';
        formatOptions.minute = '2-digit';
        formatOptions.hour12 = true;

        if (withZone) {
            formatOptions.timeZoneName = 'short';
        }
    }

    const parts = new Intl.DateTimeFormat('en-US', formatOptions).formatToParts(date);
    const part = (type) => parts.find((p) => p.type === type)?.value ?? '';

    const datePart = `${part('month')} ${part('day')}, ${part('year')}`;
    const timePart = `${part('hour')}:${part('minute')} ${part('dayPeriod').toLowerCase()}`;
    const zonePart = withZone && part('timeZoneName') ? ` (${part('timeZoneName')})` : '';

    if (dateOnly) {
        return datePart;
    }

    if (timeOnly) {
        return `${timePart}${zonePart}`.trim();
    }

    return `${datePart} ${timePart}${zonePart}`;
}
