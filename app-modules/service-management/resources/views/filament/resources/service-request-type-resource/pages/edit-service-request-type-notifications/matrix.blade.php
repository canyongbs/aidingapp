{{--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}

@php
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<div class="grid divide-gray-950/5 dark:divide-white/10 xl:divide-y">
    <div class="hidden divide-gray-950/5 dark:divide-white/10 xl:flex xl:divide-x xl:divide-y-0">
        <div class="flex-1"></div>

        <div class="grid grid-cols-3 gap-0 divide-x divide-gray-950/5 text-xs dark:divide-white/10">
            @foreach (['Managers', 'Auditors', 'Customers'] as $role)
                <div class="flex w-40 items-center justify-center p-2 text-gray-950 dark:text-white">
                    {{ $role }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="hidden divide-gray-950/5 dark:divide-white/10 xl:flex xl:divide-x xl:divide-y-0">
        <div class="flex-1"></div>

        <div class="grid grid-cols-6 divide-x divide-gray-950/5 text-xs dark:divide-white/10">
            @foreach (['Managers', 'Auditors', 'Customers'] as $role)
                @foreach (['Email', 'In-app Notification'] as $type)
                    <div class="flex w-20 items-center justify-center p-2 text-center text-gray-950 dark:text-white">
                        {{ $type }}
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="grid divide-y divide-gray-950/5 dark:divide-white/10">
        @foreach ([
        'service_request_created' => 'Service Request Created',
        'service_request_assigned' => 'Service Request Assigned',
        'service_request_update' => 'Service Request Update',
        'service_request_status_change' => 'Service Request Status Change (Non-Closed)',
        'service_request_closed' => 'Service Request Closed',
    ] as $eventSlug => $event)
            <div
                class="flex flex-col divide-y divide-gray-950/5 dark:divide-white/10 xl:flex-row xl:divide-x xl:divide-y-0">
                <div class="flex items-center px-3 py-2 text-sm text-gray-950 dark:text-white xl:flex-1">
                    {{ $event }}
                </div>

                <div
                    class="grid grid-cols-1 gap-3 divide-gray-950/5 px-3 py-2 text-sm dark:divide-white/10 xl:grid-cols-3 xl:gap-0 xl:divide-x xl:px-0 xl:py-0">
                    @foreach (['managers' => 'Managers', 'auditors' => 'Auditors', 'customers' => 'Customers'] as $roleSlug => $role)
                        <div class="flex flex-col gap-1 xl:w-40">
                            <div class="xl:hidden">
                                {{ $role }}
                            </div>

                            <div
                                class="grid h-full grid-cols-2 gap-1 divide-gray-950/5 dark:divide-white/10 xl:gap-0 xl:divide-x">
                                @foreach (['email' => 'Email', 'notification' => 'In-app Notification'] as $typeSlug => $type)
                                    <label
                                        class="flex items-center gap-2 xl:flex xl:w-20 xl:justify-center xl:px-3 xl:py-2"
                                    >
                                        <x-filament::input.checkbox
                                            :disabled="$isDisabled"
                                            :wire:model="$statePath . '.is_' . $roleSlug . '_' . $eventSlug . '_' . $typeSlug . '_enabled'"
                                        />

                                        <span class="xl:sr-only">
                                            {{ $type }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
