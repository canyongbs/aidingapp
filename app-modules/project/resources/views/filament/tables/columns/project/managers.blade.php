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
@php
    $visibleManagerLimit = 5;

    $managers = $getState();
    $visibleManagers = $managers->take($visibleManagerLimit);
    $hiddenManagers = $managers->skip($visibleManagerLimit);
@endphp

@if ($managers->isNotEmpty())
    <div class="fi-ta-text flex items-center gap-1">
        @foreach ($visibleManagers as $user)
            <x-project::avatar :user="$user" :show-name="false" size="sm" />
        @endforeach

        @if ($hiddenManagers->isNotEmpty())
            <div
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium text-gray-600 ring-2 ring-white dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-800"
                x-tooltip="{
                    content: @js($hiddenManagers->pluck('name')->implode(', ')),
                    theme: $store.theme,
                }"
            >
                +{{ $hiddenManagers->count() }}
            </div>
        @endif
    </div>
@else
    <div class="fi-ta-text">
        <span class="fi-ta-placeholder">N/A</span>
    </div>
@endif
