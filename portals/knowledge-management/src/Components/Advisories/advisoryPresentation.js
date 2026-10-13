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

/**
 * The dot shown beside each severity, keyed by the severity's admin-chosen color.
 */
const SEVERITY_DOT_CLASSES = {
    black: 'bg-gray-950',
    'light-gray': 'bg-gray-300',
    gray: 'bg-gray-400',
    'dark-gray': 'bg-gray-700',
    red: 'bg-red-500',
    orange: 'bg-orange-500',
    amber: 'bg-amber-500',
    yellow: 'bg-yellow-400',
    lime: 'bg-lime-500',
    green: 'bg-green-500',
    emerald: 'bg-emerald-500',
    teal: 'bg-teal-500',
    cyan: 'bg-cyan-500',
    sky: 'bg-sky-500',
    blue: 'bg-blue-500',
    navy: 'bg-blue-900',
    indigo: 'bg-indigo-500',
    violet: 'bg-violet-500',
    purple: 'bg-purple-500',
    fuchsia: 'bg-fuchsia-500',
    pink: 'bg-pink-500',
    rose: 'bg-rose-500',
};

/**
 * Severity colors that `BaseBadge` has no palette for, mapped to the closest one it has.
 */
const SEVERITY_BADGE_COLOR_FALLBACKS = {
    black: 'gray',
    'light-gray': 'gray',
    'dark-gray': 'gray',
    navy: 'blue',
};

export const SORT_OPTIONS = [
    { value: 'highest_severity', label: 'Highest severity first' },
    { value: 'lowest_severity', label: 'Lowest severity first' },
];

export const DEFAULT_SORT = SORT_OPTIONS[0].value;

export function getSeverityDotClass(severity) {
    return SEVERITY_DOT_CLASSES[severity?.color] ?? 'bg-gray-400';
}

export function getSeverityBadgeColor(severity) {
    return SEVERITY_BADGE_COLOR_FALLBACKS[severity?.color] ?? severity?.color ?? 'gray';
}

export function getStatusBadgeColor(status) {
    return status?.classification === 'resolved' ? 'success' : 'blue';
}
