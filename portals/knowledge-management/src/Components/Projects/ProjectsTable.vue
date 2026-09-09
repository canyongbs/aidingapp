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
    import BaseTable from '../ui/BaseTable.vue';
    import BaseTableBody from '../ui/BaseTableBody.vue';
    import BaseTableCell from '../ui/BaseTableCell.vue';
    import BaseTableCellText from '../ui/BaseTableCellText.vue';
    import BaseTableEmptyState from '../ui/BaseTableEmptyState.vue';
    import BaseTableHeader from '../ui/BaseTableHeader.vue';
    import BaseTableHeaderCell from '../ui/BaseTableHeaderCell.vue';
    import BaseTableRow from '../ui/BaseTableRow.vue';

    defineProps({
        projects: {
            type: Array,
            required: true,
        },
    });
</script>

<template>
    <BaseTableEmptyState v-if="projects.length === 0">
        <template #heading>No projects to display</template>
        <template #description>You don't currently have visibility into any projects.</template>
    </BaseTableEmptyState>

    <BaseTable v-else>
        <BaseTableHeader>
            <tr>
                <BaseTableHeaderCell class="w-64">Name</BaseTableHeaderCell>
                <BaseTableHeaderCell>Start Date</BaseTableHeaderCell>
                <BaseTableHeaderCell>Target Date</BaseTableHeaderCell>
                <BaseTableHeaderCell class="w-24">
                    <span class="sr-only">Actions</span>
                </BaseTableHeaderCell>
            </tr>
        </BaseTableHeader>
        <BaseTableBody>
            <BaseTableRow v-for="(project, idx) in projects" :key="project.id" :delay="idx * 30">
                <BaseTableCell>
                    <BaseTableCellText :text="project.name" :sub-text="project.description" />
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                    {{ formatDateTime(project.start_date, { dateOnly: true, utc: true }) ?? 'N/A' }}
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                    {{ formatDateTime(project.target_completion_date, { dateOnly: true, utc: true }) ?? 'N/A' }}
                </BaseTableCell>

                <BaseTableCell class="text-right">
                    <BaseButton
                        tag="router-link"
                        :to="{ name: 'view-project', params: { projectId: project.id } }"
                        color="gray"
                        size="sm"
                        :icon="ArrowRightIcon"
                        icon-position="after"
                    >
                        View
                    </BaseButton>
                </BaseTableCell>
            </BaseTableRow>
        </BaseTableBody>
    </BaseTable>
</template>
