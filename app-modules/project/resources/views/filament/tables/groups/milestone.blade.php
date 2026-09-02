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
    The milestone title is rendered here (rather than in the collapsible Group's title)
    so that it can be an interactive control. Filament's collapsible group toggle button
    reuses the Group title raw inside its `aria-label` attribute, so interactive markup
    there would corrupt the surrounding HTML.
    
    Filament echoes this description inside a `<p class="fi-ta-group-description">`, which
    only accepts phrasing content per the HTML content model. Keep every element here
    (span/svg/button) — a `<div>` is flow content, so the browser would auto-close the `<p>`
    early and reparent it, breaking both the layout and the `:has(+ .fi-ta-group-description)`
    CSS selector in resources/css/filament/admin/theme.css.
--}}
<span class="flex w-full items-center justify-between gap-x-3">
    @if ($canManageMilestone)
        <x-filament::link
            tag="button"
            x-on:click.stop
            wire:click="mountAction('manageMilestone', { milestone: '{{ $milestone->getKey() }}' })"
        >
            <span class="fi-sr-only">Edit milestone:</span>
            {{ $milestone->title }}
        </x-filament::link>
    @else
        <span>{{ $milestone->title }}</span>
    @endif

    {{-- Color/size classes are scoped to this call site only; the shared component ships unstyled so the dashboard header and List Projects table are unaffected. --}}
    <x-project::progress-circle
        :progress="$percentage"
        tag="span"
        class="text-sm text-primary-600 dark:text-primary-500"
    />
</span>
