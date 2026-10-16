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
    import BaseButton from '@common/BaseButton.vue';
    import BaseTable from '@common/BaseTable.vue';
    import BaseTableBody from '@common/BaseTableBody.vue';
    import BaseTableCell from '@common/BaseTableCell.vue';
    import BaseTableCellText from '@common/BaseTableCellText.vue';
    import BaseTableHeader from '@common/BaseTableHeader.vue';
    import BaseTableHeaderCell from '@common/BaseTableHeaderCell.vue';
    import BaseTableRow from '@common/BaseTableRow.vue';
    import { ArrowRightIcon } from '@heroicons/vue/16/solid';
    import formatDateTime from '../../Services/FormatDateTime.js';
    import { getStatusColor } from './advisoryPresentation.js';

    defineProps({
        advisories: {
            type: Array,
            required: true,
        },
        loading: {
            type: Boolean,
            default: false,
        },
    });
</script>

<template>
    <BaseTable :loading="loading">
        <template v-if="$slots.toolbar" #toolbar>
            <slot name="toolbar" />
        </template>

        <template v-if="$slots.empty" #empty>
            <slot name="empty" />
        </template>

        <template v-if="$slots.footer" #footer>
            <slot name="footer" />
        </template>

        <BaseTableHeader>
            <tr>
                <BaseTableHeaderCell class="w-full min-w-72">Advisory</BaseTableHeaderCell>
                <BaseTableHeaderCell class="w-44">Severity</BaseTableHeaderCell>
                <BaseTableHeaderCell class="w-44">Status</BaseTableHeaderCell>
                <BaseTableHeaderCell class="w-48 whitespace-nowrap">Last updated</BaseTableHeaderCell>
                <BaseTableHeaderCell>
                    <span class="sr-only">Actions</span>
                </BaseTableHeaderCell>
            </tr>
        </BaseTableHeader>
        <BaseTableBody>
            <BaseTableRow v-for="(advisory, idx) in advisories" :key="advisory.id" :delay="idx * 30">
                <BaseTableCell>
                    <BaseTableCellText :text="advisory.title" :sub-text="advisory.description" :sub-text-lines="2" />
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap">
                    <BaseBadge v-if="advisory.severity" :color="advisory.severity.color">
                        {{ advisory.severity.name }}
                    </BaseBadge>
                    <span v-else class="text-sm text-gray-400">—</span>
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap">
                    <BaseBadge v-if="advisory.status" :color="getStatusColor(advisory.status)">
                        {{ advisory.status.name }}
                    </BaseBadge>
                </BaseTableCell>

                <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                    <time :datetime="advisory.last_updated_at">
                        <span class="block">{{ formatDateTime(advisory.last_updated_at, { dateOnly: true }) }}</span>
                        <span class="block text-gray-500">{{
                            formatDateTime(advisory.last_updated_at, { timeOnly: true })
                        }}</span>
                    </time>
                </BaseTableCell>

                <BaseTableCell class="text-right">
                    <BaseButton
                        tag="router-link"
                        :to="{ name: 'view-advisory', params: { advisoryId: advisory.id } }"
                        color="gray"
                        size="sm"
                        :icon="ArrowRightIcon"
                        icon-position="after"
                        class="whitespace-nowrap"
                    >
                        View
                        <span class="sr-only">{{ advisory.title }}</span>
                    </BaseButton>
                </BaseTableCell>
            </BaseTableRow>
        </BaseTableBody>
    </BaseTable>
</template>
