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
<div
    class="flex w-full items-center justify-between gap-3"
    x-init="$el.closest('.fi-ta-group-header').firstElementChild.classList.add('grow')"
    x-on:click.stop
>
    <span>{{ $progress }}</span>

    @if ($milestone)
        <div class="flex items-center gap-3">
            @can('update', $milestone)
                <x-filament::link
                    tag="button"
                    icon="heroicon-m-pencil-square"
                    :wire:click="'mountAction(\'editMilestone\', { milestone:\'' . $milestone->getKey() . '\' })'"
                >
                    Edit
                </x-filament::link>
            @endcan

            @can('delete', $milestone)
                <x-filament::link
                    tag="button"
                    icon="heroicon-m-trash"
                    color="danger"
                    :wire:click="'mountAction(\'deleteMilestone\', { milestone:\'' . $milestone->getKey() . '\' })'"
                >
                    Delete
                </x-filament::link>
            @endcan
        </div>
    @endif
</div>
