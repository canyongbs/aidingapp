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
    import Pagination from '@common/portal/Pagination.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import { ComputerDesktopIcon } from '@heroicons/vue/24/outline';
    import { computed, ref, watch } from 'vue';
    import AssetStatCards from '../Components/Assets/AssetStatCards.vue';
    import AssetTable from '../Components/Assets/AssetTable.vue';
    import LoadFailedState from '../Components/LoadFailedState.vue';
    import { usePaginatedEnvelope } from '../Composables/usePaginatedEnvelope.js';
    import { apiGet } from '../Services/api.js';
    import { useAssetsData } from './loaders.js';

    const EMPTY_STATES = {
        all: { heading: 'No assets found', description: 'No assets have been assigned to your account.' },
        checked_out: {
            heading: 'No assets currently checked out',
            description: "You don't currently have any assets checked out.",
        },
        returned: { heading: 'No returned assets', description: 'No assets have been returned yet.' },
    };

    // The route data loader resolves page 1 of every filter tab (all/checked out/returned) together, so any other page
    // is fetched on demand.
    const { data: initialData } = useAssetsData();

    const activeFilter = ref('all');

    const tabs = [
        { value: 'all', label: 'All' },
        { value: 'checked_out', label: 'Checked Out' },
        { value: 'returned', label: 'Returned' },
    ];

    const { currentPage, shownEnvelope, isFetching, loadFailed, pagination, refetch } = usePaginatedEnvelope({
        initialEnvelope: computed(() => initialData.value?.[activeFilter.value] ?? null),
        usesInitialEnvelope: (page) => page === 1,
        key: (page) => ['knowledge-management', 'assets', activeFilter.value, page],
        query: (page) => apiGet('/assets', { filter: activeFilter.value, page }),
    });

    watch(activeFilter, () => {
        currentPage.value = 1;
    });

    const assets = computed(() => shownEnvelope.value?.data ?? []);
    const counts = computed(() => initialData.value?.counts ?? { total: 0, checked_out: 0, returned: 0 });
    const emptyState = computed(() => EMPTY_STATES[activeFilter.value] ?? EMPTY_STATES.all);
</script>

<template>
    <Page>
        <template #heading>Assets</template>
        <template #description>View your assigned hardware and equipment</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Assets'" />
        </template>

        <template v-if="shownEnvelope">
            <AssetStatCards :counts="counts" />

            <div class="flex flex-col gap-y-4">
                <Tabs v-model="activeFilter" :tabs="tabs" label="Asset filter tabs" id-prefix="assets" />

                <AssetTable
                    :id="`assets-panel-${activeFilter}`"
                    role="tabpanel"
                    :aria-labelledby="`assets-tab-${activeFilter}`"
                    :assets="assets"
                    :loading="isFetching"
                >
                    <template v-if="loadFailed || assets.length === 0" #empty>
                        <LoadFailedState v-if="loadFailed" heading="Could not load assets" @retry="refetch" />

                        <EmptyState role="status" v-else :icon="ComputerDesktopIcon" :contained="false">
                            <template #heading>{{ emptyState.heading }}</template>
                            <template #description>{{ emptyState.description }}</template>
                        </EmptyState>
                    </template>

                    <template v-if="!loadFailed && assets.length > 0" #footer>
                        <Pagination v-bind="pagination" />
                    </template>
                </AssetTable>
            </div>
        </template>

        <EmptyState v-else :icon="ComputerDesktopIcon">
            <template #heading>There are no assets to display.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md"> Return Home </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
