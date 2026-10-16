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
    import BaseTable from '@common/BaseTable.vue';
    import BaseTableBody from '@common/BaseTableBody.vue';
    import BaseTableCell from '@common/BaseTableCell.vue';
    import BaseTableCellText from '@common/BaseTableCellText.vue';
    import BaseTableHeader from '@common/BaseTableHeader.vue';
    import BaseTableHeaderCell from '@common/BaseTableHeaderCell.vue';
    import BaseTableLoadingState from '@common/BaseTableLoadingState.vue';
    import BaseTableRow from '@common/BaseTableRow.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import { ComputerDesktopIcon } from '@heroicons/vue/24/outline';
    import { computed, defineEmits, defineProps } from 'vue';
    import formatDateTime from '../../Services/FormatDateTime.js';

    const props = defineProps({
        assets: {
            type: Array,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
        loadingPage: {
            type: Number,
            default: null,
        },
        activeFilter: {
            type: String,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
        lastPage: {
            type: Number,
            required: true,
        },
        fromItem: {
            type: Number,
            required: true,
        },
        toItem: {
            type: Number,
            required: true,
        },
        totalItems: {
            type: Number,
            required: true,
        },
    });

    const EMPTY_STATES = {
        returned: { heading: 'No returned assets', description: 'No assets have been returned yet.' },
        checked_out: {
            heading: 'No assets currently checked out',
            description: "You don't currently have any assets checked out.",
        },
        all: { heading: 'No assets found', description: 'No assets have been assigned to your account.' },
    };

    const emptyState = computed(() => EMPTY_STATES[props.activeFilter] ?? EMPTY_STATES.all);

    const emit = defineEmits(['fetchPage']);

    function serialDisplay(asset) {
        const serial = asset?.serial_number;
        return serial && String(serial).trim() ? String(serial).trim() : 'N/A';
    }
</script>

<template>
    <Transition name="assets-fade" mode="out-in">
        <BaseTableLoadingState
            v-if="loading"
            key="skeleton"
            :columns="6"
            :rows="7"
            label="Loading assets, please wait…"
            class="mt-1"
        />

        <BaseTable
            v-else
            key="table"
            :id="`assets-panel-${activeFilter}`"
            :aria-labelledby="`assets-tab-${activeFilter}`"
            role="tabpanel"
            aria-live="polite"
            :loading="loadingPage !== null"
        >
            <template v-if="assets.length === 0" #empty>
                <EmptyState role="status" :icon="ComputerDesktopIcon" :contained="false">
                    <template #heading>{{ emptyState.heading }}</template>
                    <template #description>{{ emptyState.description }}</template>
                </EmptyState>
            </template>

            <template v-if="assets.length > 0" #footer>
                <Pagination
                    :current-page="currentPage"
                    :last-page="lastPage"
                    :from-item="fromItem"
                    :to-item="toItem"
                    :total-items="totalItems"
                    :loading-page="loadingPage"
                    @fetchPreviousPage="emit('fetchPage', currentPage - 1)"
                    @fetchNextPage="emit('fetchPage', currentPage + 1)"
                    @fetchPage="emit('fetchPage', $event)"
                />
            </template>

            <BaseTableHeader>
                <tr>
                    <BaseTableHeaderCell class="w-64">Name</BaseTableHeaderCell>
                    <BaseTableHeaderCell>Type</BaseTableHeaderCell>
                    <BaseTableHeaderCell>Serial Number</BaseTableHeaderCell>
                    <BaseTableHeaderCell>Status</BaseTableHeaderCell>
                    <BaseTableHeaderCell>Checkout Date</BaseTableHeaderCell>
                    <BaseTableHeaderCell>Return Date</BaseTableHeaderCell>
                </tr>
            </BaseTableHeader>
            <BaseTableBody>
                <BaseTableRow v-for="(item, idx) in assets" :key="item.id" :delay="idx * 30">
                    <BaseTableCell>
                        <BaseTableCellText :text="item.asset?.name" :sub-text="item.asset?.description" />
                    </BaseTableCell>

                    <BaseTableCell class="text-sm text-gray-600">
                        {{ item.asset?.type?.name ?? '—' }}
                    </BaseTableCell>

                    <BaseTableCell>
                        <BaseBadge color="gray" class="font-mono">{{ serialDisplay(item.asset) }}</BaseBadge>
                    </BaseTableCell>

                    <BaseTableCell>
                        <BaseBadge v-if="item.status === 'returned'" color="success">Returned</BaseBadge>
                        <BaseBadge v-else color="warning">Checked Out</BaseBadge>
                    </BaseTableCell>

                    <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                        {{ formatDateTime(item.checked_out_at) ?? '—' }}
                    </BaseTableCell>

                    <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                        {{ formatDateTime(item.checked_in_at) ?? '' }}
                    </BaseTableCell>
                </BaseTableRow>
            </BaseTableBody>
        </BaseTable>
    </Transition>
</template>

<style>
    .assets-fade-enter-active,
    .assets-fade-leave-active {
        transition:
            opacity 0.16s ease,
            transform 0.16s ease;
    }
    .assets-fade-enter-from,
    .assets-fade-leave-to {
        opacity: 0;
        transform: translateY(5px);
    }
</style>
