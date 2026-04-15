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
    import { computed, onMounted, ref, watch } from 'vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Page from '../Components/Page.vue';
    import AssetStatCards from '../Components/Assets/AssetStatCards.vue';
    import AssetFilterTabs from '../Components/Assets/AssetFilterTabs.vue';
    import AssetTable from '../Components/Assets/AssetTable.vue';
    import { consumer } from '../Services/Consumer';

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    const { get } = consumer();

    const activeFilter = ref('all');
    const loading = ref(true);

    const assets = ref([]);
    const counts = ref({ total: 0, checked_out: 0, returned: 0 });

    const currentPage = ref(1);
    const lastPage = ref(1);
    const fromItem = ref(0);
    const toItem = ref(0);
    const totalItems = ref(0);

    const tabs = computed(() => [
        { key: 'all', label: 'All' },
        { key: 'checked_out', label: 'Checked Out' },
        { key: 'returned', label: 'Returned' },
    ]);

    async function fetchAssets(page = 1) {
        loading.value = true;

        try {
            const response = await get(`${props.apiUrl}/assets`, {
                filter: activeFilter.value,
                page,
            });

            const envelope = response.data;
            const paged = envelope.data;

            assets.value = paged.data ?? [];
            counts.value = envelope.counts ?? { total: 0, checked_out: 0, returned: 0 };

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

    onMounted(() => fetchAssets(1));
</script>

<template>
    <Page>
        <template #heading>Assets</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Assets'" />
        </template>

        <AssetStatCards :counts="counts" />

        <AssetFilterTabs v-model="activeFilter" :tabs="tabs" />

        <AssetTable
            :assets="assets"
            :loading="loading"
            :active-filter="activeFilter"
            :current-page="currentPage"
            :last-page="lastPage"
            :from-item="fromItem"
            :to-item="toItem"
            :total-items="totalItems"
            @fetchPage="fetchAssets"
        />
    </Page>
</template>
