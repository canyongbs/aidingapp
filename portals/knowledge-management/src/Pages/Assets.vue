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
    import { onMounted, ref } from 'vue';
    import AppLoading from '../Components/AppLoading.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import EmptyState from '../Components/EmptyState.vue';
    import Page from '../Components/Page.vue';
    import { consumer } from '../Services/Consumer';

    const checkedInAssets = ref({});
    const checkedOutAssets = ref({});
    const { get } = consumer();
    const loading = ref(true);

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    async function getAssets() {
        loading.value = true;
        const response = await get(`${props.apiUrl}/assets`);

        if (response.error) {
            throw new Error(response.error);
        }

        return response.data;
    }

    onMounted(async () => {
        await getAssets()
            .then((response) => {
                checkedInAssets.value = response.checkedInAssets;
                checkedOutAssets.value = response.checkedOutAssets;
                loading.value = false;
            })
            .catch((error) => {
                if (error.response && (error.response.status === 401 || error.response.status === 404)) {
                    loading.value = false;
                } else {
                    console.error('Error fetching assets:', error);
                }
            });
    });
</script>

<template>
    <div v-if="loading">
        <AppLoading />
    </div>
    <Page v-else>
        <template #heading> Assets </template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Assets'" />
        </template>

        <div class="grid divide-y divide-gray-200">
            <div v-if="checkedOutAssets?.length > 0">
                <h3 class="text-xl">Assets</h3>
                <div
                    class="mt-4 overflow-hidden rounded bg-gray-200 shadow-sm ring-1 ring-black/5 grid gap-px divide-y-0 lg:grid-cols-2"
                >
                    <div
                        v-for="checkedOutAsset in checkedOutAssets"
                        :key="checkedOutAsset?.id"
                        class="group relative bg-white p-6 focus-within:bg-gray-50"
                    >
                        <div class="grid">
                            <div class="w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true" />
                                    {{ checkedOutAsset.asset?.name }}
                                </h3>
                                <div class="mt-2">
                                    <span class="py-1 text-sm rounded">
                                        Description: {{ checkedOutAsset.asset?.description }}<br />
                                        Serial Number: {{ checkedOutAsset.asset?.serial_number }}<br />
                                        Type: {{ checkedOutAsset.asset?.type?.name }}<br />
                                        Date Checked Out: {{ checkedOutAsset.formatted_checked_out_at }}<br />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <EmptyState v-else>
                <template #heading>There are no checked out assets to display.</template>
            </EmptyState>

            <div v-if="checkedInAssets?.length > 0">
                <h3 class="text-xl">Returned Assets</h3>
                <div
                    class="mt-4 overflow-hidden rounded bg-gray-200 shadow-sm ring-1 ring-black/5 grid gap-px divide-y-0 lg:grid-cols-2"
                >
                    <div
                        v-for="checkedInAsset in checkedInAssets"
                        :key="checkedInAsset.id"
                        class="group relative bg-white p-6 focus-within:bg-gray-50"
                    >
                        <div class="grid">
                            <div class="w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">
                                    {{ checkedInAsset.asset?.name }}
                                </h3>
                                <div class="mt-2">
                                    <span class="py-1 text-sm rounded">
                                        Description: {{ checkedInAsset.asset?.description }}<br />
                                        Serial Number: {{ checkedInAsset.asset?.serial_number }}<br />
                                        Type: {{ checkedInAsset.asset?.type?.name }}<br />
                                        Date Returned: {{ checkedInAsset.formatted_checked_in_at }}<br />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <EmptyState v-else>
                <template #heading>There are no checked in assets to display.</template>
            </EmptyState>
        </div>
    </Page>
</template>
