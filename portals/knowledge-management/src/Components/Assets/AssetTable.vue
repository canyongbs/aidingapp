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
    import BaseTable from '../ui/BaseTable.vue';
    import Pagination from '../Pagination.vue';
    import BaseStatusPill from '../ui/BaseStatusPill.vue';

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

    function truncate(str, maxLength = 30) {
        if (!str) return '';
        return str.length > maxLength ? str.slice(0, maxLength) + '\u2026' : str;
    }

    function serialDisplay(asset) {
        const serial = asset?.serial_number;
        return serial && String(serial).trim() ? String(serial).trim() : 'N/A';
    }
</script>

<template>
    <Transition name="assets-fade" mode="out-in">
        <div
            v-if="loading"
            key="skeleton"
            role="status"
            aria-busy="true"
            aria-label="Loading assets"
            class="mt-1 animate-pulse overflow-x-auto rounded-[var(--rounding-lg)] border border-gray-200"
        >
            <table class="w-full text-sm text-left">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-64"><div class="h-3 w-14 rounded bg-gray-200"></div></th>
                        <th class="px-4 py-3"><div class="h-3 w-12 rounded bg-gray-200"></div></th>
                        <th class="px-4 py-3"><div class="h-3 w-24 rounded bg-gray-200"></div></th>
                        <th class="px-4 py-3"><div class="h-3 w-14 rounded bg-gray-200"></div></th>
                        <th class="px-4 py-3"><div class="h-3 w-24 rounded bg-gray-200"></div></th>
                        <th class="px-4 py-3"><div class="h-3 w-20 rounded bg-gray-200"></div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr v-for="row in 7" :key="row">
                        <td class="px-4 py-4">
                            <div class="mb-2 h-3.5 w-44 rounded bg-gray-200"></div>
                            <div class="h-2.5 w-56 rounded bg-gray-100"></div>
                        </td>
                        <td class="px-4 py-4"><div class="h-3 w-28 rounded bg-gray-200"></div></td>
                        <td class="px-4 py-4"><div class="h-5 w-24 rounded bg-gray-100"></div></td>
                        <td class="px-4 py-4"><div class="h-6 w-24 rounded-full bg-gray-100"></div></td>
                        <td class="px-4 py-4"><div class="h-3 w-24 rounded bg-gray-200"></div></td>
                        <td class="px-4 py-4"><div class="h-3 w-20 rounded bg-gray-100"></div></td>
                    </tr>
                </tbody>
            </table>
            <span class="sr-only">Loading assets, please wait…</span>
        </div>

        <div
            v-else-if="assets.length === 0"
            key="empty"
            role="status"
            class="mt-1 flex flex-col items-center justify-center gap-4 py-20 rounded-[var(--rounding-lg)] border border-dashed border-gray-200 bg-white text-center"
        >
            <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100">
                <svg
                    class="h-8 w-8 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
                    />
                </svg>
            </span>
            <div>
                <p class="text-base font-semibold text-gray-700">
                    <template v-if="activeFilter === 'returned'">No returned assets</template>
                    <template v-else-if="activeFilter === 'checked_out'">No assets currently checked out</template>
                    <template v-else>No assets found</template>
                </p>
                <p class="mt-1 text-sm text-gray-400 max-w-xs mx-auto">
                    <template v-if="activeFilter === 'returned'">No assets have been returned yet.</template>
                    <template v-else-if="activeFilter === 'checked_out'"
                        >You don't currently have any assets checked out.</template
                    >
                    <template v-else>No assets have been assigned to your account.</template>
                </p>
            </div>
        </div>

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
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th scope="col" class="w-64 px-4 py-3">Name</th>
                        <th scope="col" class="px-4 py-3">Device Type &amp; Age</th>
                        <th scope="col" class="px-4 py-3">Serial Number</th>
                        <th scope="col" class="px-4 py-3">Location</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3">Last Activity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr
                        v-for="(item, idx) in assets"
                        :key="item.id"
                        class="asset-row transition-colors duration-100 hover:bg-gray-50/70"
                        :style="{ '--row-delay': `${idx * 30}ms` }"
                    >
                            <td class="px-4 py-3.5 align-top">
                                <p
                                    class="max-w-[14rem] truncate text-sm font-semibold leading-5 text-gray-900"
                                    :title="item.asset?.name"
                                >
                                    {{ item.asset?.name ?? '—' }}
                                </p>
                                <p
                                    v-if="item.asset?.description"
                                    class="mt-0.5 max-w-[14rem] truncate text-xs leading-5 text-gray-500"
                                    :title="item.asset.description"
                                >
                                    {{ truncate(item.asset.description, 30) }}
                                </p>
                            </td>

                            <td class="px-4 py-3.5 align-top text-gray-600">
                                <p class="text-sm font-medium text-gray-800">{{ item.asset?.type?.name ?? '—' }}</p>
                                <p v-if="item.asset?.purchase_age" class="mt-0.5 text-xs leading-5 text-gray-500">
                                    {{ item.asset.purchase_age }}
                                </p>
                            </td>

                            <td class="px-4 py-3.5 align-top">
                                <span
                                    class="rounded-[var(--rounding-sm)] border border-gray-200 bg-gray-50 px-2 py-0.5 font-mono text-xs text-gray-600"
                                >
                                    {{ serialDisplay(item.asset) }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5 align-top text-sm text-gray-700">
                                {{ item.asset?.location?.name ?? '—' }}
                            </td>

                            <td class="px-4 py-3.5 align-top">
                                <BaseStatusPill v-if="item.status === 'returned'" tone="success">
                                    Returned
                                </BaseStatusPill>
                                <BaseStatusPill v-else tone="warning" :pulse="true">
                                    Checked Out
                                </BaseStatusPill>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3.5 align-top text-xs font-medium text-gray-600">
                                {{ item.last_activity ?? '—' }}
                            </td>
                    </tr>
                </tbody>
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

    @keyframes assetRowIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .asset-row {
        animation: assetRowIn 0.2s ease both;
        animation-delay: var(--row-delay, 0ms);
    }
</style>
