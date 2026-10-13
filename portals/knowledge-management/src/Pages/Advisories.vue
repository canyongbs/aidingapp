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
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import PageCard from '@common/portal/PageCard.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import { ClipboardDocumentCheckIcon } from '@heroicons/vue/24/outline';
    import { useQuery } from '@pinia/colada';
    import { computed, onUnmounted, ref, watch } from 'vue';
    import AdvisoriesTable from '../Components/Advisories/AdvisoriesTable.vue';
    import { DEFAULT_SORT, SORT_OPTIONS } from '../Components/Advisories/advisoryPresentation.js';
    import ServiceMonitorStatusBanner from '../Components/ServiceMonitors/ServiceMonitorStatusBanner.vue';
    import BaseSearchInput from '../Components/ui/BaseSearchInput.vue';
    import BaseSelect from '../Components/ui/BaseSelect.vue';
    import BaseTableEmptyState from '../Components/ui/BaseTableEmptyState.vue';
    import { apiGet } from '../Services/api.js';
    import { useAdvisoriesData } from './loaders.js';

    const DEFAULT_CLASSIFICATION = 'open';

    // Page 1 of the active advisories with the default sort arrives via the route data loader; any other view is fetched on demand.
    const { data: initialData } = useAdvisoriesData();

    const classification = ref(DEFAULT_CLASSIFICATION);
    const currentPage = ref(1);
    const search = ref('');
    const appliedSearch = ref('');
    const severity = ref('');
    const status = ref('');
    const sort = ref(DEFAULT_SORT);

    let searchTimeout = null;

    watch(search, (value) => {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            appliedSearch.value = value.trim();
        }, 300);
    });

    onUnmounted(() => clearTimeout(searchTimeout));

    // Each tab has its own statuses, so a status picked on one tab can never match on the other.
    watch(classification, () => {
        status.value = '';
    });

    watch([classification, appliedSearch, severity, status, sort], () => {
        currentPage.value = 1;
    });

    const isFiltered = computed(() => appliedSearch.value !== '' || severity.value !== '' || status.value !== '');

    const usesInitialData = computed(
        () =>
            classification.value === DEFAULT_CLASSIFICATION &&
            currentPage.value === 1 &&
            !isFiltered.value &&
            sort.value === DEFAULT_SORT,
    );

    const pageQuery = useQuery({
        key: () => [
            'knowledge-management',
            'advisories',
            classification.value,
            currentPage.value,
            appliedSearch.value,
            severity.value,
            status.value,
            sort.value,
        ],
        query: () =>
            apiGet('/advisories', {
                classification: classification.value,
                page: currentPage.value,
                search: appliedSearch.value || undefined,
                severity: severity.value || undefined,
                status: status.value || undefined,
                sort: sort.value,
            }),
        enabled: () => !usesInitialData.value,
    });

    const currentEnvelope = computed(() =>
        usesInitialData.value ? (initialData.value ?? null) : (pageQuery.data.value ?? null),
    );

    // Keeps the previous results visible while a new tab, page, search, or filter loads.
    const shownEnvelope = ref(null);
    watch(
        currentEnvelope,
        (envelope) => {
            if (envelope) {
                shownEnvelope.value = envelope;
            }
        },
        { immediate: true },
    );

    const advisories = computed(() => shownEnvelope.value?.data ?? []);
    const counts = computed(() => shownEnvelope.value?.counts ?? { open: 0, resolved: 0 });
    const lastPage = computed(() => shownEnvelope.value?.meta?.last_page ?? 1);
    const fromItem = computed(() => shownEnvelope.value?.meta?.from ?? 0);
    const toItem = computed(() => shownEnvelope.value?.meta?.to ?? 0);
    const totalItems = computed(() => shownEnvelope.value?.meta?.total ?? 0);
    const isFetching = computed(() => !usesInitialData.value && pageQuery.isLoading.value);
    const loadingPage = computed(() => (isFetching.value ? currentPage.value : null));
    const loadFailed = computed(() => !usesInitialData.value && !isFetching.value && Boolean(pageQuery.error.value));

    const tabs = computed(() => [
        { value: 'open', label: `Active (${counts.value.open})` },
        { value: 'resolved', label: `Resolved (${counts.value.resolved})` },
    ]);

    const classificationLabel = computed(() => (classification.value === 'open' ? 'active' : 'resolved'));
    const hasAdvisoriesInTab = computed(() => counts.value[classification.value] > 0);

    const severityOptions = computed(() => [
        { value: '', label: 'All severities' },
        ...(shownEnvelope.value?.severities ?? []).map((option) => ({ value: option.id, label: option.name })),
    ]);

    const statusOptions = computed(() => [
        { value: '', label: 'All statuses' },
        ...(shownEnvelope.value?.statuses ?? [])
            .filter((option) => option.classification === classification.value)
            .map((option) => ({ value: option.id, label: option.name })),
    ]);

    function clearFilters() {
        search.value = '';
        appliedSearch.value = '';
        severity.value = '';
        status.value = '';
    }

    function fetchPage(page) {
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    }
</script>

<template>
    <Page>
        <template #heading>Advisories</template>
        <template #description>Important notices and security advisories</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Advisories'" />
        </template>

        <template v-if="shownEnvelope">
            <ServiceMonitorStatusBanner
                v-if="counts.open > 0"
                status="degraded"
                title="Active advisories"
                :description="`There ${counts.open === 1 ? 'is' : 'are'} currently ${counts.open} active ${counts.open === 1 ? 'advisory' : 'advisories'}.`"
            />
            <ServiceMonitorStatusBanner
                v-else
                status="operational"
                title="No active advisories"
                description="There are currently no active advisories."
            />

            <PageCard>
                <div class="-mx-6 -mt-4">
                    <Tabs v-model="classification" :tabs="tabs" contained />
                </div>

                <div v-if="hasAdvisoriesInTab" class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <BaseSearchInput
                        v-model="search"
                        label="Search advisories"
                        placeholder="Search advisories…"
                        class="lg:max-w-sm"
                    />
                    <BaseSelect v-model="severity" :options="severityOptions" label="Severity" class="lg:w-48" />
                    <BaseSelect v-model="status" :options="statusOptions" label="Status" class="lg:w-48" />
                    <BaseSelect v-model="sort" :options="SORT_OPTIONS" label="Sort" class="lg:ml-auto lg:w-56" />
                </div>

                <div :class="['transition-opacity', isFetching && 'opacity-60']" :aria-busy="isFetching">
                    <BaseTableEmptyState v-if="loadFailed">
                        <p class="text-base font-semibold text-gray-700">Could not load advisories</p>
                        <p class="mt-1 text-sm text-gray-400">Something went wrong while loading these results.</p>
                        <BaseButton color="gray" size="md" class="mt-4" @click="pageQuery.refetch()">
                            Try again
                        </BaseButton>
                    </BaseTableEmptyState>

                    <EmptyState
                        v-else-if="!hasAdvisoriesInTab && classification === 'open'"
                        :icon="ClipboardDocumentCheckIcon"
                        :contained="false"
                    >
                        <template #heading>No active advisories</template>
                        <template #description>Resolved advisories are available in the history.</template>
                        <template v-if="counts.resolved > 0" #actions>
                            <BaseButton color="gray" size="md" @click="classification = 'resolved'">
                                View resolved advisories
                            </BaseButton>
                        </template>
                    </EmptyState>

                    <EmptyState v-else-if="!hasAdvisoriesInTab" :icon="ClipboardDocumentCheckIcon" :contained="false">
                        <template #heading>No resolved advisories</template>
                        <template #description>Advisories will appear here once they have been resolved.</template>
                    </EmptyState>

                    <BaseTableEmptyState v-else-if="advisories.length === 0">
                        <p class="text-base font-semibold text-gray-700">No advisories match your search</p>
                        <p class="mt-1 text-sm text-gray-400">Try a different search or clear the filters.</p>
                        <BaseButton v-if="isFiltered" color="gray" size="md" class="mt-4" @click="clearFilters">
                            Clear filters
                        </BaseButton>
                    </BaseTableEmptyState>

                    <template v-else>
                        <AdvisoriesTable :advisories="advisories" />

                        <Pagination
                            v-if="lastPage > 1"
                            class="-mx-6 mt-4"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :from-item="fromItem"
                            :to-item="toItem"
                            :total-items="totalItems"
                            :loading-page="loadingPage"
                            @fetchPreviousPage="fetchPage(currentPage - 1)"
                            @fetchNextPage="fetchPage(currentPage + 1)"
                            @fetchPage="fetchPage"
                        />
                        <p v-else class="mt-4 text-sm text-gray-500">
                            Showing {{ totalItems }} {{ classificationLabel }}
                            {{ totalItems === 1 ? 'advisory' : 'advisories' }}
                        </p>
                    </template>
                </div>
            </PageCard>
        </template>

        <EmptyState v-else :icon="ClipboardDocumentCheckIcon">
            <template #heading>There are no advisories to display.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md"> Return Home </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
