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
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import Page from '@common/portal/Page.vue';
    import PageCard from '@common/portal/PageCard.vue';
    import { useQuery } from '@pinia/colada';
    import { computed, ref, watch } from 'vue';
    import AssetFilterTabs from '../Components/Assets/AssetFilterTabs.vue';
    import AssetStatCards from '../Components/Assets/AssetStatCards.vue';
    import AssetTable from '../Components/Assets/AssetTable.vue';
    import { apiGet } from '../Services/api.js';
    import { useAssetsData } from './loaders.js';

    // The default "all" filter, page 1, arrives via the route data loader; changing the
    // filter or page fetches (and caches) on demand via Pinia Colada.
    const { data: initialData } = useAssetsData();

    const activeFilter = ref('all');
    const currentPage = ref(1);

    const tabs = computed(() => [
        { key: 'all', label: 'All' },
        { key: 'checked_out', label: 'Checked Out' },
        { key: 'returned', label: 'Returned' },
    ]);

    const isDefaultView = computed(() => activeFilter.value === 'all' && currentPage.value === 1);

    const pageQuery = useQuery({
        key: () => ['knowledge-management', 'assets', activeFilter.value, currentPage.value],
        query: () => apiGet('/assets', { filter: activeFilter.value, page: currentPage.value }),
        enabled: () => !isDefaultView.value,
        staleTime: 1000 * 60 * 5,
    });

    const currentEnvelope = computed(() => (isDefaultView.value ? initialData.value : (pageQuery.data.value ?? null)));

    // Keeps the previous filter/page visible while a new one loads.
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

    // Only true before the first paint; filter/page switches keep showing `shownEnvelope`.
    const loading = computed(() => shownEnvelope.value === null);

    const loadingEnvelope = computed(() => !isDefaultView.value && pageQuery.isLoading.value);
    const loadingPage = computed(() => (loadingEnvelope.value ? currentPage.value : null));

    const assets = computed(() => shownEnvelope.value?.data ?? []);
    const counts = computed(() => shownEnvelope.value?.counts ?? { total: 0, checked_out: 0, returned: 0 });

    const lastPage = computed(() => shownEnvelope.value?.meta?.last_page ?? 1);
    const fromItem = computed(() => shownEnvelope.value?.meta?.from ?? 0);
    const toItem = computed(() => shownEnvelope.value?.meta?.to ?? 0);
    const totalItems = computed(() => shownEnvelope.value?.meta?.total ?? 0);

    watch(activeFilter, () => {
        currentPage.value = 1;
    });

    function fetchPage(page) {
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    }
</script>

<template>
    <Page>
        <template #heading>Assets</template>
        <template #description> View your assigned hardware and equipment </template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Assets'" />
        </template>

        <PageCard>
            <AssetStatCards :counts="counts" />

            <AssetFilterTabs v-model="activeFilter" :tabs="tabs" />

            <AssetTable
                :assets="assets"
                :loading="loading"
                :loading-page="loadingPage"
                :active-filter="activeFilter"
                :current-page="currentPage"
                :last-page="lastPage"
                :from-item="fromItem"
                :to-item="toItem"
                :total-items="totalItems"
                @fetchPage="fetchPage"
            />
        </PageCard>
    </Page>
</template>
