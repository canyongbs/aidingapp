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
    import BaseBadge from '@common/BaseBadge.vue';
    import { ChartPieIcon } from '@heroicons/vue/20/solid';
    import formatDateTime from '../../Services/FormatDateTime.js';
    import BaseTable from '../ui/BaseTable.vue';
    import BaseTableBody from '../ui/BaseTableBody.vue';
    import BaseTableCell from '../ui/BaseTableCell.vue';
    import BaseTableEmptyState from '../ui/BaseTableEmptyState.vue';
    import BaseTableHeader from '../ui/BaseTableHeader.vue';
    import BaseTableHeaderCell from '../ui/BaseTableHeaderCell.vue';
    import BaseTableRow from '../ui/BaseTableRow.vue';

    defineProps({
        groups: {
            type: Array,
            required: true,
        },
    });
</script>

<template>
    <BaseTableEmptyState v-if="groups.length === 0">
        <template #heading>No pipeline tasks</template>
        <template #description>There are no tasks to display for this pipeline yet.</template>
    </BaseTableEmptyState>

    <BaseTable v-else>
        <BaseTableHeader>
            <tr>
                <BaseTableHeaderCell class="w-64">Task Name</BaseTableHeaderCell>
                <BaseTableHeaderCell>Stage</BaseTableHeaderCell>
                <BaseTableHeaderCell>Start Date</BaseTableHeaderCell>
                <BaseTableHeaderCell>Target Date</BaseTableHeaderCell>
            </tr>
        </BaseTableHeader>
        <BaseTableBody>
            <template
                v-for="(group, groupIndex) in groups"
                :key="`${group.milestone_id ?? 'no-milestone'}-${groupIndex}`"
            >
                <tr class="bg-gray-50/70">
                    <td colspan="4" class="px-4 py-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-gray-700">{{ group.milestone_title }}</span>
                            <BaseBadge v-if="group.progress_percentage !== null" color="gray" :icon="ChartPieIcon">
                                Progress: {{ group.progress_percentage }}%
                            </BaseBadge>
                        </div>
                    </td>
                </tr>

                <BaseTableRow v-if="group.entries.length === 0">
                    <BaseTableCell class="text-gray-400">No tasks yet</BaseTableCell>
                    <BaseTableCell class="text-gray-400">N/A</BaseTableCell>
                    <BaseTableCell class="text-gray-400">N/A</BaseTableCell>
                    <BaseTableCell class="text-gray-400">N/A</BaseTableCell>
                </BaseTableRow>

                <BaseTableRow v-for="entry in group.entries" :key="entry.id">
                    <BaseTableCell class="text-sm text-gray-900">{{ entry.name }}</BaseTableCell>
                    <BaseTableCell>
                        <BaseBadge color="gray">{{ entry.stage ?? 'N/A' }}</BaseBadge>
                    </BaseTableCell>
                    <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                        {{ formatDateTime(entry.start_date, { dateOnly: true, utc: true }) ?? 'N/A' }}
                    </BaseTableCell>
                    <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                        {{ formatDateTime(entry.due, { dateOnly: true, utc: true }) ?? 'N/A' }}
                    </BaseTableCell>
                </BaseTableRow>
            </template>
        </BaseTableBody>
    </BaseTable>
</template>
