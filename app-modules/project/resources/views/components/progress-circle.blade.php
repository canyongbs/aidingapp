{{--
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
--}}

{{--
    The `tag` prop lets callers embedding this inside a Filament table Group description
    render it as a `<span>` instead of the default `<div>`, since Group descriptions only
    accept phrasing content (see the "Group descriptions must be phrasing content only"
    rule). Text styling (size/color) is intentionally left off the label below and must be
    passed in via `class`, so it only affects the call sites that opt into it.
--}}

@props([
    'progress' => 0,
    'tag' => 'div',
])

<{{ $tag }} {{ $attributes->merge(['class' => 'flex items-center gap-1.5']) }}>
    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" aria-hidden="true">
        <circle
            cx="10"
            cy="10"
            r="8"
            fill="none"
            stroke="currentColor"
            stroke-width="5"
            class="text-gray-200 dark:text-gray-700"
        />
        <circle
            cx="10"
            cy="10"
            r="8"
            fill="none"
            stroke="currentColor"
            stroke-width="5"
            stroke-linecap="round"
            stroke-dasharray="{{ 2 * 3.14159 * 8 }}"
            stroke-dashoffset="{{ 2 * 3.14159 * 8 * (1 - $progress / 100) }}"
            transform="rotate(-90 10 10)"
            class="text-primary-600 dark:text-primary-500"
        />
    </svg>
    <span>Progress: {{ $progress }}%</span>
</{{ $tag }}>
