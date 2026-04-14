<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
    import { computed, onMounted, ref, watch } from 'vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Page from '../Components/Page.vue';
    import Pagination from '../Components/Pagination.vue';
    import { consumer } from '../Services/Consumer';

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    const { get } = consumer();

    const activeFilter = ref('all');
    const searchQuery = ref('');
    const loading = ref(true);

    const assets = ref([]);
    const counts = ref({ total: 0, checked_out: 0, available: 0 });

    const currentPage = ref(1);
    const lastPage = ref(1);
    const fromItem = ref(0);
    const toItem = ref(0);
    const totalItems = ref(0);

    const tabs = computed(() => [
        { key: 'all', label: 'All Assets' },
        { key: 'available', label: 'Available' },
        { key: 'checked_out', label: 'Checked Out' },
    ]);

    async function fetchAssets(page = 1) {
        loading.value = true;

        try {
            const response = await get(`${props.apiUrl}/assets`, {
                filter: activeFilter.value,
                page,
                ...(searchQuery.value.trim() ? { search: searchQuery.value.trim() } : {}),
            });

            const envelope = response.data;
            const paged = envelope.data;

            assets.value = paged.data ?? [];
            counts.value = envelope.counts ?? { total: 0, checked_out: 0, available: 0 };

            currentPage.value = paged.current_page ?? 1;
            lastPage.value = paged.last_page ?? 1;
            fromItem.value = paged.from ?? 0;
            toItem.value = paged.to ?? 0;
            totalItems.value = paged.total ?? 0;
        } catch (error) {
            assets.value = [];
            console.error('Error fetching assets:', error);
        } finally {
            loading.value = false;
        }
    }

    watch(activeFilter, () => fetchAssets(1));

    const searchDebounce = ref(null);
    watch(searchQuery, () => {
        clearTimeout(searchDebounce.value);
        searchDebounce.value = setTimeout(() => fetchAssets(1), 400);
    });

    onMounted(() => fetchAssets(1));

    function truncate(str, n = 30) {
        if (!str) return '';
        return str.length > n ? str.slice(0, n) + '\u2026' : str;
    }

    function serialDisplay(asset) {
        const s = asset?.serial_number;
        return s && String(s).trim() ? String(s).trim() : 'N/A';
    }
</script>

<template>
    <Page>
        <template #heading>Assets</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Assets'" />
        </template>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="p-5 bg-white rounded-[var(--rounding-lg)] border border-gray-200 shadow-xs">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">All Assets</p>
                        <p class="text-4xl font-bold leading-none tabular-nums text-gray-800">
                            {{ counts.total }}
                        </p>
                    </div>
                    <span
                        class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-[rgba(var(--primary-50),1)]"
                    >
                        <svg
                            class="w-5 h-5 text-[rgba(var(--primary-500),1)]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
                            />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="p-5 bg-white rounded-[var(--rounding-lg)] border border-gray-200 shadow-xs">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Available</p>
                        <p class="text-4xl font-bold leading-none tabular-nums text-gray-800">
                            {{ counts.available }}
                        </p>
                    </div>
                    <span class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-green-50">
                        <svg
                            class="w-5 h-5 text-green-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"
                            />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="p-5 bg-white rounded-[var(--rounding-lg)] border border-gray-200 shadow-xs">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Checked Out</p>
                        <p class="text-4xl font-bold leading-none tabular-nums text-gray-800">
                            {{ counts.checked_out }}
                        </p>
                    </div>
                    <span class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-orange-50">
                        <svg
                            class="w-5 h-5 text-orange-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"
                            />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-2">
            <div class="border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px" role="tablist" aria-label="Asset filter tabs">
                    <li v-for="tab in tabs" :key="tab.key" role="presentation">
                        <button
                            type="button"
                            role="tab"
                            :id="`assets-tab-${tab.key}`"
                            :aria-selected="activeFilter === tab.key"
                            :aria-controls="`assets-panel-${tab.key}`"
                            @click="activeFilter = tab.key"
                            :class="[
                                'inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap',
                                activeFilter === tab.key
                                    ? 'border-[rgba(var(--primary-500),1)] text-[rgba(var(--primary-600),1)]'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            ]"
                        >
                            {{ tab.label }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="relative w-full sm:w-72 pb-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                        />
                    </svg>
                </div>
                <input
                    v-model="searchQuery"
                    type="search"
                    aria-label="Search assets by name, type, or serial number"
                    placeholder="Search assets…"
                    class="block w-full py-2 pl-10 pr-4 text-sm text-gray-900 bg-white border border-gray-300 rounded-[var(--rounding)] transition focus:outline-none focus:ring-2 focus:ring-[rgba(var(--primary-500),1)] focus:border-[rgba(var(--primary-500),1)]"
                />
            </div>
        </div>

        <Transition name="assets-fade" mode="out-in">
            <div
                v-if="loading"
                key="skeleton"
                role="status"
                aria-busy="true"
                aria-label="Loading assets"
                class="animate-pulse overflow-x-auto rounded-[var(--rounding-lg)] border border-gray-200 mt-1"
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
                        <tr v-for="i in 7" :key="i">
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
                        <template v-if="searchQuery">No results for "{{ searchQuery }}"</template>
                        <template v-else-if="activeFilter === 'available'">No available assets</template>
                        <template v-else-if="activeFilter === 'checked_out'">No assets currently checked out</template>
                        <template v-else>No assets found</template>
                    </p>
                    <p class="mt-1 text-sm text-gray-400 max-w-xs mx-auto">
                        <template v-if="searchQuery">Try a different name, type, or serial number.</template>
                        <template v-else-if="activeFilter === 'available'">No assets have been returned yet.</template>
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
                <div class="overflow-x-auto rounded-t-[var(--rounding-lg)] border border-gray-200 shadow-xs">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead
                            class="border-b border-gray-200 bg-gray-50 text-xs uppercase tracking-wider text-gray-500"
                        >
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold w-64">Name</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Device Type &amp; Age</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Serial Number</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Location</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Last Activity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr
                                v-for="(item, idx) in assets"
                                :key="item.id"
                                class="asset-row transition-colors duration-100 hover:bg-gray-50/70"
                                :style="{ '--row-delay': `${idx * 30}ms` }"
                            >
                                <td class="px-4 py-4">
                                    <p
                                        class="max-w-[14rem] truncate font-semibold leading-tight text-gray-900"
                                        :title="item.asset?.name"
                                    >
                                        {{ truncate(item.asset?.name ?? '—', 30) }}
                                    </p>
                                    <p
                                        v-if="item.asset?.description"
                                        class="mt-0.5 max-w-[14rem] truncate text-xs leading-snug text-gray-400"
                                        :title="item.asset.description"
                                    >
                                        {{ truncate(item.asset.description, 30) }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    <p class="font-medium text-gray-800">{{ item.asset?.type?.name ?? '—' }}</p>
                                    <p v-if="item.asset?.purchase_age" class="mt-0.5 text-xs text-gray-400">
                                        {{ item.asset.purchase_age }}
                                    </p>
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        class="rounded border border-gray-200 bg-gray-50 px-2 py-0.5 font-mono text-xs text-gray-500"
                                    >
                                        {{ serialDisplay(item.asset) }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ item.asset?.location?.name ?? '—' }}
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        v-if="item.status === 'available'"
                                        class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 flex-shrink-0 rounded-full bg-green-500"
                                            aria-hidden="true"
                                        ></span>
                                        Available
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-800"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 flex-shrink-0 animate-pulse rounded-full bg-orange-500"
                                            aria-hidden="true"
                                        ></span>
                                        Checked Out
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-xs text-gray-600">
                                    {{ item.last_activity ?? '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination
                    v-if="lastPage > 1"
                    :current-page="currentPage"
                    :last-page="lastPage"
                    :from-article="fromItem"
                    :to-article="toItem"
                    :total-articles="totalItems"
                    @fetchPreviousPage="fetchAssets(currentPage - 1)"
                    @fetchNextPage="fetchAssets(currentPage + 1)"
                    @fetchPage="fetchAssets"
                />
            </div>
        </Transition>
    </Page>
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
