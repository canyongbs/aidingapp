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
    import BaseButton from '@common/BaseButton.vue';
    import { ArrowRightIcon } from '@heroicons/vue/16/solid';
    import formatDateTime from '../../Services/FormatDateTime.js';
    import formatRelativeTime from '../../Services/FormatRelativeTime.js';
    import BaseTable from '../ui/BaseTable.vue';
    import BaseTableBody from '../ui/BaseTableBody.vue';
    import BaseTableCell from '../ui/BaseTableCell.vue';
    import BaseTableCellText from '../ui/BaseTableCellText.vue';
    import BaseTableHeader from '../ui/BaseTableHeader.vue';
    import BaseTableHeaderCell from '../ui/BaseTableHeaderCell.vue';
    import BaseTableRow from '../ui/BaseTableRow.vue';
    import BaseTableSortableHeaderCell from '../ui/BaseTableSortableHeaderCell.vue';
    import ServiceMonitorHistorySparkline from './ServiceMonitorHistorySparkline.vue';
    import ServiceMonitorStatusPill from './ServiceMonitorStatusPill.vue';
    import { formatUptimePercentage, getMonitorTypeIcon } from './serviceMonitorStatuses.js';

    defineProps({
        monitors: {
            type: Array,
            required: true,
        },
        sort: {
            type: String,
            required: true,
        },
        direction: {
            type: String,
            required: true,
        },
        now: {
            type: Number,
            required: true,
        },
    });

    const emit = defineEmits(['sort']);

    const sortableColumns = [
        { column: 'name', label: 'Monitor', class: 'min-w-64' },
        { column: 'status', label: 'Status' },
        { column: 'thirty_day_uptime', label: '30-day uptime' },
        { column: 'twelve_month_uptime', label: '12-month uptime' },
        { column: 'last_checked_at', label: 'Last checked' },
        { column: 'frequency', label: 'Monitoring cadence' },
    ];
</script>

<template>
    <BaseTable>
        <BaseTableHeader>
            <tr>
                <BaseTableSortableHeaderCell
                    v-for="sortableColumn in sortableColumns"
                    :key="sortableColumn.column"
                    :column="sortableColumn.column"
                    :sort="sort"
                    :direction="direction"
                    :class="sortableColumn.class"
                    @sort="emit('sort', $event)"
                >
                    {{ sortableColumn.label }}
                </BaseTableSortableHeaderCell>
                <BaseTableHeaderCell class="whitespace-nowrap">30-day history</BaseTableHeaderCell>
                <BaseTableHeaderCell>
                    <span class="sr-only">Actions</span>
                </BaseTableHeaderCell>
            </tr>
        </BaseTableHeader>
        <BaseTableBody>
            <BaseTableRow v-for="(monitor, idx) in monitors" :key="monitor.id" :delay="idx * 30">
                <BaseTableCell>
                    <div class="flex items-center gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-[var(--rounding-md)] bg-gray-100 text-gray-700"
                            :title="monitor.monitor_type_label"
                        >
                            <component
                                :is="getMonitorTypeIcon(monitor.monitor_type)"
                                class="size-5"
                                aria-hidden="true"
                            />
                            <span class="sr-only">{{ monitor.monitor_type_label }}</span>
                        </span>
                        <BaseTableCellText
                            class="min-w-0"
                            :text="monitor.name"
                            :sub-text="monitor.description"
                            :sub-text-max-length="60"
                        />
                    </div>
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap">
                    <ServiceMonitorStatusPill :status="monitor.status" />
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap text-sm tabular-nums text-gray-900">
                    {{ formatUptimePercentage(monitor.thirty_day_uptime_percentage) }}
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap text-sm tabular-nums text-gray-900">
                    {{ formatUptimePercentage(monitor.twelve_month_uptime_percentage) }}
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                    <time
                        v-if="monitor.last_checked_at"
                        :datetime="monitor.last_checked_at"
                        :title="formatDateTime(monitor.last_checked_at)"
                    >
                        {{ formatRelativeTime(monitor.last_checked_at, now) }}
                    </time>
                    <template v-else>Never</template>
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                    Every {{ monitor.frequency_label }}
                </BaseTableCell>

                <BaseTableCell>
                    <ServiceMonitorHistorySparkline :history="monitor.history" />
                </BaseTableCell>

                <BaseTableCell class="text-right">
                    <BaseButton
                        tag="router-link"
                        :to="{ name: 'view-service-monitor', params: { serviceMonitorId: monitor.id } }"
                        color="gray"
                        size="sm"
                        :icon="ArrowRightIcon"
                        icon-position="after"
                        class="whitespace-nowrap"
                    >
                        View details
                        <span class="sr-only">for {{ monitor.name }}</span>
                    </BaseButton>
                </BaseTableCell>
            </BaseTableRow>
        </BaseTableBody>
    </BaseTable>
</template>
