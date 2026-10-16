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
    import BaseCalloutMeta from '@common/BaseCalloutMeta.vue';
    import BaseSearchInput from '@common/BaseSearchInput.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import { ExclamationTriangleIcon, MagnifyingGlassIcon, SignalIcon } from '@heroicons/vue/24/outline';
    import { useQuery } from '@pinia/colada';
    import { computed, ref, watch } from 'vue';
    import ServiceMonitorsTable from '../Components/ServiceMonitors/ServiceMonitorsTable.vue';
    import ServiceMonitorStatusCounts from '../Components/ServiceMonitors/ServiceMonitorStatusCounts.vue';
    import { getStatus } from '../Components/ServiceMonitors/serviceMonitorStatuses.js';
    import { useNow } from '../Composables/useNow.js';
    import { apiGet } from '../Services/api.js';
    import formatDateTime, { resolveTimezone } from '../Services/FormatDateTime.js';
    import { useServiceMonitorData } from './loaders.js';

    const DEFAULT_SORT = 'name';
    const DEFAULT_DIRECTION = 'asc';

    // Page 1 with the default sort arrives via the route data loader; any other view is fetched on demand.
    const { data: initialData } = useServiceMonitorData();

    const now = useNow();

    const currentPage = ref(1);
    const search = ref('');
    const sort = ref(DEFAULT_SORT);
    const direction = ref(DEFAULT_DIRECTION);

    const appliedSearch = computed(() => search.value.trim());

    watch([appliedSearch, sort, direction], () => {
        currentPage.value = 1;
    });

    const usesInitialData = computed(
        () =>
            currentPage.value === 1 &&
            appliedSearch.value === '' &&
            sort.value === DEFAULT_SORT &&
            direction.value === DEFAULT_DIRECTION,
    );

    const pageQuery = useQuery({
        key: () => [
            'knowledge-management',
            'service-monitors',
            currentPage.value,
            appliedSearch.value,
            sort.value,
            direction.value,
        ],
        query: () =>
            apiGet('/status', {
                page: currentPage.value,
                search: appliedSearch.value || undefined,
                sort: sort.value,
                direction: direction.value,
                timezone: resolveTimezone(),
            }),
        enabled: () => !usesInitialData.value,
    });

    const currentEnvelope = computed(() =>
        usesInitialData.value ? (initialData.value ?? null) : (pageQuery.data.value ?? null),
    );

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

    const monitors = computed(() => shownEnvelope.value?.data ?? []);
    const summary = computed(() => shownEnvelope.value?.summary ?? null);
    const summaryStatus = computed(() => getStatus(summary.value?.status));
    const lastPage = computed(() => shownEnvelope.value?.meta?.last_page ?? 1);
    const fromItem = computed(() => shownEnvelope.value?.meta?.from ?? 0);
    const toItem = computed(() => shownEnvelope.value?.meta?.to ?? 0);
    const totalItems = computed(() => shownEnvelope.value?.meta?.total ?? 0);
    const isFetching = computed(() => !usesInitialData.value && pageQuery.isLoading.value);
    const loadingPage = computed(() => (isFetching.value ? currentPage.value : null));
    const loadFailed = computed(() => !usesInitialData.value && !isFetching.value && Boolean(pageQuery.error.value));

    const summaryCopy = computed(() => {
        const total = summary.value?.total_count ?? 0;
        const affected = (summary.value?.status_counts?.degraded ?? 0) + (summary.value?.status_counts?.outage ?? 0);
        const unchecked = summary.value?.status_counts?.unknown ?? 0;
        const operational = total - unchecked;

        switch (summary.value?.status) {
            case 'operational':
                if (unchecked > 0) {
                    return {
                        title: 'All checked systems operational',
                        description: `${operational} of ${total} monitored services ${operational === 1 ? 'is' : 'are'} running as expected. ${unchecked} ${unchecked === 1 ? "hasn't" : "haven't"} completed ${unchecked === 1 ? 'its' : 'their'} first check yet.`,
                    };
                }

                return {
                    title: 'All systems operational',
                    description: 'All monitored services are running as expected.',
                };
            case 'degraded':
                return {
                    title: 'Some systems are experiencing issues',
                    description: `${affected} of ${total} monitored ${total === 1 ? 'service' : 'services'} ${affected === 1 ? 'is' : 'are'} not fully operational.`,
                };
            default:
                return {
                    title: 'Awaiting status data',
                    description: "Monitored services haven't completed their first checks yet.",
                };
        }
    });

    function toggleSort(column) {
        if (sort.value === column) {
            direction.value = direction.value === 'asc' ? 'desc' : 'asc';

            return;
        }

        sort.value = column;
        direction.value = 'asc';
    }

    function fetchPage(page) {
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    }
</script>

<template>
    <Page>
        <template #heading>Status</template>
        <template #description>Real-time status of services and systems</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Status'" />
        </template>

        <template v-if="summary && summary.total_count > 0">
            <BaseCallout
                :tone="summaryStatus.tone"
                :icon="summaryStatus.icon"
                :title="summaryCopy.title"
                :description="summaryCopy.description"
            >
                <template v-if="summary.last_checked_at" #meta>
                    <BaseCalloutMeta label="Last updated">
                        <time :datetime="summary.last_checked_at">{{ formatDateTime(summary.last_checked_at) }}</time>
                    </BaseCalloutMeta>
                </template>
            </BaseCallout>

            <ServiceMonitorsTable
                :monitors="monitors"
                :sort="sort"
                :direction="direction"
                :now="now"
                :loading="isFetching"
                @sort="toggleSort"
            >
                <template #toolbar>
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <ServiceMonitorStatusCounts
                            :total-count="summary.total_count"
                            :status-counts="summary.status_counts"
                        />
                        <BaseSearchInput
                            v-model="search"
                            :debounce="300"
                            label="Search monitors"
                            placeholder="Search monitors…"
                            class="md:max-w-xs"
                        />
                    </div>
                </template>

                <template v-if="loadFailed || monitors.length === 0" #empty>
                    <EmptyState role="status" v-if="loadFailed" :icon="ExclamationTriangleIcon" :contained="false">
                        <template #heading>Could not load monitors</template>
                        <template #description>Something went wrong while loading these results.</template>
                        <template #actions>
                            <BaseButton color="gray" size="md" @click="pageQuery.refetch()">Try again</BaseButton>
                        </template>
                    </EmptyState>

                    <EmptyState role="status" v-else :icon="MagnifyingGlassIcon" :contained="false">
                        <template #heading>No monitors match your search</template>
                        <template #description>Try searching for a different name or description.</template>
                    </EmptyState>
                </template>

                <template v-if="!loadFailed && monitors.length > 0" #footer>
                    <Pagination
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
                </template>
            </ServiceMonitorsTable>
        </template>

        <EmptyState v-else :icon="SignalIcon">
            <template #heading>There are no service monitors to display.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md"> Return Home </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
