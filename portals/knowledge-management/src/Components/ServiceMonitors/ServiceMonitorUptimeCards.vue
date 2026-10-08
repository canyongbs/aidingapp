<!--
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
-->
<script setup>
    import { CalendarDaysIcon, CalendarIcon, ChartBarIcon } from '@heroicons/vue/24/outline';
    import BaseCard from '../ui/BaseCard.vue';
    import { formatUptimePercentage } from './serviceMonitorStatuses.js';

    defineProps({
        uptimePercentages: {
            type: Object,
            required: true,
        },
    });

    const periods = [
        { key: 'twenty_four_hours', label: '24-hour uptime', icon: ChartBarIcon },
        { key: 'seven_days', label: '7-day uptime', icon: CalendarIcon },
        { key: 'thirty_days', label: '30-day uptime', icon: CalendarDaysIcon },
        { key: 'ninety_days', label: '90-day uptime', icon: CalendarDaysIcon },
        { key: 'twelve_months', label: '12-month uptime', icon: CalendarDaysIcon },
    ];
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
        <BaseCard v-for="period in periods" :key="period.key" class="flex items-center gap-4">
            <span
                class="flex size-11 shrink-0 items-center justify-center rounded-[var(--rounding-md)] bg-[rgba(var(--primary-50),1)] text-[rgba(var(--primary-500),1)]"
                aria-hidden="true"
            >
                <component :is="period.icon" class="size-5 stroke-2" />
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-500">{{ period.label }}</p>
                <p
                    class="mt-1 text-2xl font-semibold leading-none tabular-nums text-gray-900"
                    :title="uptimePercentages[period.key] === null ? 'Not enough monitoring history yet' : undefined"
                >
                    {{ formatUptimePercentage(uptimePercentages[period.key]) }}
                </p>
            </div>
        </BaseCard>
    </div>
</template>
