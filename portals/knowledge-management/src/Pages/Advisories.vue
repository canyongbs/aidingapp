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
    import BaseCallout from '@common/BaseCallout.vue';
    import BaseSearchInput from '@common/BaseSearchInput.vue';
    import BaseSelect from '@common/BaseSelect.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import {
        ClipboardDocumentCheckIcon,
        ExclamationTriangleIcon,
        MagnifyingGlassIcon,
    } from '@heroicons/vue/24/outline';
    import { computed, ref, watch } from 'vue';
    import AdvisoriesTable from '../Components/Advisories/AdvisoriesTable.vue';
    import { DEFAULT_SORT, SORT_OPTIONS } from '../Components/Advisories/advisoryPresentation.js';
    import { usePaginatedEnvelope } from '../Composables/usePaginatedEnvelope.js';
    import { apiGet } from '../Services/api.js';
    import { useAdvisoriesData } from './loaders.js';

    const DEFAULT_CLASSIFICATION = 'open';

    // Page 1 of the active advisories with the default sort arrives via the route data loader; any other view is fetched on demand.
    const { data: initialData } = useAdvisoriesData();

    const classification = ref(DEFAULT_CLASSIFICATION);
    const search = ref('');
    const severity = ref('');
    const status = ref('');
    const sort = ref(DEFAULT_SORT);

    const appliedSearch = computed(() => search.value.trim());

    const isFiltered = computed(() => appliedSearch.value !== '' || severity.value !== '' || status.value !== '');

    const { currentPage, shownEnvelope, isFetching, loadFailed, pagination, refetch } = usePaginatedEnvelope({
        initialEnvelope: initialData,
        usesInitialEnvelope: (page) =>
            classification.value === DEFAULT_CLASSIFICATION &&
            page === 1 &&
            !isFiltered.value &&
            sort.value === DEFAULT_SORT,
        key: (page) => [
            'knowledge-management',
            'advisories',
            classification.value,
            page,
            appliedSearch.value,
            severity.value,
            status.value,
            sort.value,
        ],
        query: (page) =>
            apiGet('/advisories', {
                classification: classification.value,
                page,
                search: appliedSearch.value || undefined,
                severity: severity.value || undefined,
                status: status.value || undefined,
                sort: sort.value,
            }),
    });

    // Each tab has its own statuses, so a status picked on one tab can never match on the other.
    watch(classification, () => {
        status.value = '';
    });

    watch([classification, appliedSearch, severity, status, sort], () => {
        currentPage.value = 1;
    });

    const advisories = computed(() => shownEnvelope.value?.data ?? []);
    const counts = computed(() => shownEnvelope.value?.counts ?? { open: 0, resolved: 0 });

    const tabs = computed(() => [
        { value: 'open', label: 'Active', badge: counts.value.open },
        { value: 'resolved', label: 'Resolved', badge: counts.value.resolved },
    ]);

    const hasAdvisoriesInTab = computed(() => counts.value[classification.value] > 0);
    const showsEmptyState = computed(
        () => loadFailed.value || !hasAdvisoriesInTab.value || advisories.value.length === 0,
    );

    const severityOptions = computed(() => [
        { value: '', label: 'All severities' },
        ...(shownEnvelope.value?.severities ?? []).map((option) => ({ value: option.id, label: option.name })),
    ]);

    const statusOptions = computed(() => [
        { value: '', label: 'All statuses' },
        ...(shownEnvelope.value?.statuses ?? []).map((option) => ({ value: option.id, label: option.name })),
    ]);

    function clearFilters() {
        search.value = '';
        severity.value = '';
        status.value = '';
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
            <BaseCallout
                v-if="counts.open > 0"
                tone="warning"
                title="Active advisories"
                :description="`There ${counts.open === 1 ? 'is' : 'are'} currently ${counts.open} active ${counts.open === 1 ? 'advisory' : 'advisories'}.`"
            />
            <BaseCallout
                v-else
                tone="success"
                title="No active advisories"
                description="There are currently no active advisories."
            />

            <div class="flex flex-col gap-y-4">
                <Tabs v-model="classification" :tabs="tabs" label="Advisory tabs" id-prefix="advisories" />

                <AdvisoriesTable
                    :id="`advisories-panel-${classification}`"
                    role="tabpanel"
                    :aria-labelledby="`advisories-tab-${classification}`"
                    :advisories="advisories"
                    :loading="isFetching"
                >
                    <template v-if="hasAdvisoriesInTab" #toolbar>
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                            <BaseSearchInput
                                v-model="search"
                                :debounce="300"
                                label="Search advisories"
                                placeholder="Search advisories…"
                                class="lg:max-w-sm"
                            />
                            <BaseSelect
                                v-model="severity"
                                :options="severityOptions"
                                label="Severity"
                                class="lg:w-48"
                            />
                            <BaseSelect v-model="status" :options="statusOptions" label="Status" class="lg:w-48" />
                            <BaseSelect
                                v-model="sort"
                                :options="SORT_OPTIONS"
                                label="Sort"
                                class="lg:ml-auto lg:w-56"
                            />
                        </div>
                    </template>

                    <template v-if="showsEmptyState" #empty>
                        <EmptyState role="status" v-if="loadFailed" :icon="ExclamationTriangleIcon" :contained="false">
                            <template #heading>Could not load advisories</template>
                            <template #description>Something went wrong while loading these results.</template>
                            <template #actions>
                                <BaseButton color="gray" size="md" @click="refetch">Try again</BaseButton>
                            </template>
                        </EmptyState>

                        <EmptyState
                            role="status"
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

                        <EmptyState
                            role="status"
                            v-else-if="!hasAdvisoriesInTab"
                            :icon="ClipboardDocumentCheckIcon"
                            :contained="false"
                        >
                            <template #heading>No resolved advisories</template>
                            <template #description>Advisories will appear here once they have been resolved.</template>
                        </EmptyState>

                        <EmptyState role="status" v-else :icon="MagnifyingGlassIcon" :contained="false">
                            <template #heading>No advisories match your search</template>
                            <template #description>Try a different search or clear the filters.</template>
                            <template v-if="isFiltered" #actions>
                                <BaseButton color="gray" size="md" @click="clearFilters">Clear filters</BaseButton>
                            </template>
                        </EmptyState>
                    </template>

                    <template v-if="!showsEmptyState" #footer>
                        <Pagination v-bind="pagination" />
                    </template>
                </AdvisoriesTable>
            </div>
        </template>

        <EmptyState v-else :icon="ClipboardDocumentCheckIcon">
            <template #heading>There are no advisories to display.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md"> Return Home </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
