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
    use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
    use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
    use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;

    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $roleCount = count(ServiceRequestTypeEmailTemplateRole::cases());
    $channelCount = count(ServiceRequestNotificationChannel::cases());
@endphp

<div class="divide-gray-950/5 grid xl:divide-y dark:divide-white/10">
    <style>
        @media (min-width: 1280px) {
            .matrix-xl-roles {
                grid-template-columns: repeat({{ $roleCount }}, minmax(0, 1fr));
            }
        }
    </style>
    <div class="divide-gray-950/5 hidden xl:flex xl:divide-x xl:divide-y-0 dark:divide-white/10">
        <div class="flex-1"></div>

        <div
            class="divide-gray-950/5 grid gap-0 divide-x text-xs dark:divide-white/10"
            style="grid-template-columns: repeat({{ $roleCount }}, minmax(0, 1fr))"
        >
            @foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role)
                <div class="flex w-32 items-center justify-center p-2 text-gray-950 dark:text-white">
                    {{ $role->getLabel() }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="divide-gray-950/5 hidden xl:flex xl:divide-x xl:divide-y-0 dark:divide-white/10">
        <div class="flex-1"></div>

        <div
            class="divide-gray-950/5 grid divide-x text-xs dark:divide-white/10"
            style="grid-template-columns: repeat({{ $roleCount * $channelCount }}, minmax(0, 1fr))"
        >
            @foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role)
                @foreach (ServiceRequestNotificationChannel::cases() as $channel)
                    <div class="flex w-16 items-center justify-center p-2 text-center text-gray-950 dark:text-white">
                        {{ $channel->getLabel() }}
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="divide-gray-950/5 grid divide-y dark:divide-white/10">
        @foreach (ServiceRequestEmailTemplateType::cases() as $templateType)
            @php
                $eventSlug = $templateType->getEventSlug();
            @endphp

            <div
                class="divide-gray-950/5 flex flex-col divide-y xl:flex-row xl:divide-x xl:divide-y-0 dark:divide-white/10"
            >
                <div
                    class="flex items-center px-3 py-2 text-sm text-gray-950 xl:flex-1 dark:text-white"
                    @if ($templateType === ServiceRequestEmailTemplateType::StatusChange)
                        x-tooltip.raw="Applies to all status changes other than those in a closed classification"
                    @endif
                >
                    {{ $templateType->getViewLabel() }}
                </div>

                <div
                    class="matrix-xl-roles divide-gray-950/5 grid grid-cols-1 gap-3 px-3 py-2 text-sm xl:gap-0 xl:divide-x xl:px-0 xl:py-0 dark:divide-white/10"
                >
                    @foreach (ServiceRequestTypeEmailTemplateRole::cases() as $templateRole)
                        @php
                            $roleSlug = $templateRole->value . "s";
                        @endphp

                        <div class="flex flex-col gap-1 xl:w-32">
                            <div class="xl:hidden">{{ $templateRole->getLabel() }}s</div>

                            <div
                                class="divide-gray-950/5 grid h-full grid-cols-2 gap-1 xl:gap-0 xl:divide-x dark:divide-white/10"
                            >
                                @foreach (ServiceRequestNotificationChannel::cases() as $channel)
                                    @php
                                        $isSurveyResponse = $templateType === ServiceRequestEmailTemplateType::SurveyResponse;
                                        $typeSlug = $channel->value;
                                        $shouldShow = ! (
                                            ($isSurveyResponse && in_array($roleSlug, ["managers", "auditors", "assigned_managers"])) ||
                                            ($isSurveyResponse && $roleSlug === "customers" && $typeSlug === "notification")
                                        );
                                    @endphp

                                    @if ($shouldShow)
                                        <label
                                            class="flex items-center gap-2 xl:flex xl:w-16 xl:justify-center xl:px-3 xl:py-2"
                                        >
                                            <x-filament::input.checkbox
                                                :disabled="$isDisabled"
                                                :wire:model="$statePath . '.is_' . $roleSlug . '_' . $eventSlug . '_' . $channel->value . '_enabled'"
                                            />
                                            <span class="xl:sr-only">{{ $channel->getLabel() }}</span>
                                        </label>
                                    @else
                                        <div class="xl:flex xl:w-16 xl:justify-center xl:px-3 xl:py-2"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
