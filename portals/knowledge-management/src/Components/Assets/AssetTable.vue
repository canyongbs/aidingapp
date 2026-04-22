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
    import Pagination from '../Pagination.vue';
    import BaseBadge from '../ui/BaseBadge.vue';
    import BaseTable from '../ui/BaseTable.vue';
    import BaseTableBody from '../ui/BaseTableBody.vue';
    import BaseTableCell from '../ui/BaseTableCell.vue';
    import BaseTableCellText from '../ui/BaseTableCellText.vue';
    import BaseTableEmptyState from '../ui/BaseTableEmptyState.vue';
    import BaseTableHeader from '../ui/BaseTableHeader.vue';
    import BaseTableHeaderCell from '../ui/BaseTableHeaderCell.vue';
    import BaseTableLoadingState from '../ui/BaseTableLoadingState.vue';
    import BaseTableRow from '../ui/BaseTableRow.vue';

    defineProps({
        assets: {
            type: Array,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
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

        <BaseTableEmptyState
            v-else-if="assets.length === 0"
            key="empty"
            class="mt-1"
        >
            <template v-if="activeFilter === 'returned'">
                <p class="text-base font-semibold text-gray-700">No returned assets</p>
                <p class="mt-1 text-sm text-gray-400 max-w-xs mx-auto">No assets have been returned yet.</p>
            </template>
            <template v-else-if="activeFilter === 'checked_out'">
                <p class="text-base font-semibold text-gray-700">No assets currently checked out</p>
                <p class="mt-1 text-sm text-gray-400 max-w-xs mx-auto">
                    You don't currently have any assets checked out.
                </p>
            </template>
            <template v-else>
                <p class="text-base font-semibold text-gray-700">No assets found</p>
                <p class="mt-1 text-sm text-gray-400 max-w-xs mx-auto">
                    No assets have been assigned to your account.
                </p>
            </template>
        </BaseTableEmptyState>

        <div
            v-else
            key="table"
            :id="`assets-panel-${activeFilter}`"
            :aria-labelledby="`assets-tab-${activeFilter}`"
            role="tabpanel"
            aria-live="polite"
            class="mt-1"
        >
            <BaseTable>
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
                    <BaseTableRow
                        v-for="(item, idx) in assets"
                        :key="item.id"
                        :delay="idx * 30"
                    >
                        <BaseTableCell>
                            <BaseTableCellText
                                :text="item.asset?.name"
                                :sub-text="item.asset?.description"
                            />
                        </BaseTableCell>

                        <BaseTableCell class="text-sm text-gray-600">
                            {{ item.asset?.type?.name ?? '—' }}
                        </BaseTableCell>

                        <BaseTableCell>
                            <BaseBadge :mono="true">{{ serialDisplay(item.asset) }}</BaseBadge>
                        </BaseTableCell>

                        <BaseTableCell>
                            <BaseBadge v-if="item.status === 'returned'" tone="success">Returned</BaseBadge>
                            <BaseBadge v-else tone="warning" :pulse="true">Checked Out</BaseBadge>
                        </BaseTableCell>

                        <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                            {{ item.checked_out_at ?? '—' }}
                        </BaseTableCell>

                        <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                            {{ item.checked_in_at ?? '' }}
                        </BaseTableCell>
                    </BaseTableRow>
                </BaseTableBody>
            </BaseTable>

            <Pagination
                v-if="lastPage > 1"
                :current-page="currentPage"
                :last-page="lastPage"
                :from-article="fromItem"
                :to-article="toItem"
                :total-articles="totalItems"
                @fetchPreviousPage="emit('fetchPage', currentPage - 1)"
                @fetchNextPage="emit('fetchPage', currentPage + 1)"
                @fetchPage="emit('fetchPage', $event)"
            />
        </div>
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
