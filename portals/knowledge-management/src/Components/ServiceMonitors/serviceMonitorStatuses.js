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
import {
    CheckIcon,
    ClockIcon,
    CodeBracketIcon,
    DocumentMagnifyingGlassIcon,
    ExclamationTriangleIcon,
    GlobeAltIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

/**
 * Each service monitor status returned by the API, mapped to its label, tone (for callouts), colour name (for badges and
 * status swatches), icon and copy. Components own the classes for each tone and colour.
 */
const STATUSES = {
    operational: {
        label: 'Operational',
        tone: 'success',
        color: 'green',
        icon: CheckIcon,
        description: 'This monitor is operating normally with no known issues.',
        noteTitle: 'Service healthy',
        noteDescription:
            'This monitor is operating normally with no known issues. Service availability and performance are within expected levels.',
    },
    degraded: {
        label: 'Degraded',
        tone: 'warning',
        color: 'orange',
        icon: ExclamationTriangleIcon,
        description: 'This monitor is reachable, but some checks failed within the last 24 hours.',
        noteTitle: 'Service degraded',
        noteDescription:
            'The service is currently reachable, but recent checks have failed intermittently. You may experience occasional disruptions.',
    },
    outage: {
        label: 'Outage',
        tone: 'danger',
        color: 'red',
        icon: XMarkIcon,
        description: 'The most recent check of this monitor failed, so the service may be unavailable.',
        noteTitle: 'Service unavailable',
        noteDescription:
            'The service is not responding as expected. You may be unable to use it until the issue is resolved.',
    },
    unknown: {
        label: 'No data',
        tone: 'neutral',
        color: 'gray',
        icon: ClockIcon,
        description: "This monitor hasn't completed its first check yet.",
        noteTitle: 'Awaiting data',
        noteDescription: 'Status information will appear here once this monitor has been checked.',
    },
};

/**
 * The statuses in the order they are shown in legends and counts.
 */
export const STATUS_ORDER = ['operational', 'degraded', 'outage', 'unknown'];

export function getStatus(status) {
    return STATUSES[status] ?? STATUSES.unknown;
}

const MONITOR_TYPE_ICONS = {
    availability: GlobeAltIcon,
    keyword_match: DocumentMagnifyingGlassIcon,
    api_endpoint: CodeBracketIcon,
};

export function getMonitorTypeIcon(monitorType) {
    return MONITOR_TYPE_ICONS[monitorType] ?? GlobeAltIcon;
}

/**
 * @param {number|null} percentage
 * @returns {string}
 */
export function formatUptimePercentage(percentage) {
    return percentage === null || percentage === undefined ? 'N/A' : `${Number(percentage).toFixed(2)}%`;
}
