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

    $serviceRequestType = $this->getSelectedServiceRequestType();
    $roles = ServiceRequestTypeEmailTemplateRole::cases();
@endphp

<x-filament-panels::page>
    {{ $this->filtersForm }}

    @if ($serviceRequestType)
        @php
            $preferences = $serviceRequestType->emailPreferences->keyBy(
                fn ($preference) => implode(':', [
                    $preference->service_request_email_template_type->value,
                    $preference->service_request_email_template_role->value,
                    $preference->notification_channel->value,
                ]),
            );
        @endphp

        <x-filament::section heading="Notifications and Alerts">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[58rem] divide-y divide-gray-950/5 text-sm dark:divide-white/10">
                    <thead class="text-gray-950 dark:text-white">
                        <tr class="divide-x divide-gray-950/5 dark:divide-white/10">
                            <th class="w-full px-3 py-2 text-left font-medium"></th>
                            @foreach ($roles as $role)
                                <th class="px-3 py-2 text-center font-medium" colspan="2">{{ $role->getLabel() }}</th>
                            @endforeach
                        </tr>
                        <tr class="divide-x divide-gray-950/5 dark:divide-white/10">
                            <th class="px-3 py-2"></th>
                            @foreach ($roles as $role)
                                @foreach (ServiceRequestNotificationChannel::cases() as $channel)
                                    <th class="w-20 px-3 py-2 text-center font-medium">{{ $channel->getLabel() }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-950/5 dark:divide-white/10">
                        @foreach (ServiceRequestEmailTemplateType::cases() as $templateType)
                            <tr class="divide-x divide-gray-950/5 dark:divide-white/10">
                                <th class="px-3 py-2 text-left font-medium text-gray-950 dark:text-white">
                                    {{ $templateType->getViewLabel() }}
                                </th>
                                @foreach ($roles as $role)
                                    @foreach (ServiceRequestNotificationChannel::cases() as $channel)
                                        @php
                                            $isShown =
                                                $templateType !== ServiceRequestEmailTemplateType::SurveyResponse ||
                                                ($role === ServiceRequestTypeEmailTemplateRole::Customer && $channel === ServiceRequestNotificationChannel::Email);
                                            $preferenceKey = implode(':', [$templateType->value, $role->value, $channel->value]);
                                        @endphp

                                        <td class="w-20 px-3 py-2 text-center">
                                            @if ($isShown)
                                                <x-filament::input.checkbox
                                                    :checked="$preferences->get($preferenceKey)?->is_enabled ?? false"
                                                    disabled
                                                />
                                            @endif
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{ $this->emailTemplatesForm }}
    @endif
</x-filament-panels::page>
