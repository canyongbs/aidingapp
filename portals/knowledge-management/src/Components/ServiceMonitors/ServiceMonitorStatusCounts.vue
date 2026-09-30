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
    import { computed } from 'vue';
    import { STATUS_ORDER, getStatus } from './serviceMonitorStatuses.js';

    const props = defineProps({
        totalCount: {
            type: Number,
            required: true,
        },
        statusCounts: {
            type: Object,
            required: true,
        },
    });

    const counts = computed(() =>
        STATUS_ORDER.filter((status) => (props.statusCounts[status] ?? 0) > 0).map((status) => ({
            status,
            count: props.statusCounts[status],
            ...getStatus(status),
        })),
    );
</script>

<template>
    <ul class="flex flex-wrap items-center gap-x-2 gap-y-1" aria-label="Monitors by status">
        <li
            class="inline-flex items-center gap-2 rounded-[var(--rounding-md)] bg-[rgba(var(--primary-50),1)] px-3 py-1.5 text-sm font-semibold text-[rgba(var(--primary-700),1)]"
        >
            All monitors
            <span class="tabular-nums">{{ totalCount }}</span>
        </li>
        <li
            v-for="item in counts"
            :key="item.status"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700"
        >
            <span :class="['size-2 rounded-full', item.barClass]" aria-hidden="true" />
            {{ item.label }}
            <span class="font-semibold tabular-nums text-gray-900">{{ item.count }}</span>
        </li>
    </ul>
</template>
