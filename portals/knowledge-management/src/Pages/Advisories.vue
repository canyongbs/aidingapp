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
    import { onMounted, ref } from 'vue';
    import BaseBadge from '../../../../resources/js/components/BaseBadge.vue';
    import BaseButton from '../../../../resources/js/components/BaseButton.vue';
    import LoadingSpinner from '../../../../resources/js/components/LoadingSpinner.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import EmptyState from '../Components/EmptyState.vue';
    import Page from '../Components/Page.vue';
    import ResourceList from '../Components/ResourceList.vue';
    import { consumer } from '../Services/Consumer';

    const advisories = ref([]);
    const { get } = consumer();
    const loading = ref(true);
    const currentPage = ref(1);
    const nextPageUrl = ref(null);
    const prevPageUrl = ref(null);
    const lastPage = ref(null);
    const perPage = ref(10);
    const hasMore = ref(false);

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    onMounted(() => {
        fetchAdvisories();
    });

    const formatDate = (date) => {
        const d = new Date(date);
        return d
            .toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
            })
            .replace(' at', '');
    };

    const loadMore = () => {
        if (nextPageUrl.value) {
            currentPage.value++;
            fetchAdvisories();
        }
    };

    const severityTextColor = (severity) => {
        const allowedColors = {
            slate: 'text-slate-600',
            gray: 'text-gray-600',
            zinc: 'text-zinc-600',
            neutral: 'text-neutral-600',
            stone: 'text-stone-600',
            red: 'text-red-600',
            orange: 'text-orange-600',
            amber: 'text-amber-600',
            yellow: 'text-yellow-600',
            lime: 'text-lime-600',
            green: 'text-green-600',
            emerald: 'text-emerald-600',
            teal: 'text-teal-600',
            cyan: 'text-cyan-600',
            sky: 'text-sky-600',
            blue: 'text-blue-600',
            indigo: 'text-indigo-600',
            violet: 'text-violet-600',
            purple: 'text-purple-600',
            fuchsia: 'text-fuchsia-600',
            pink: 'text-pink-600',
            rose: 'text-rose-600',
        };

        return allowedColors[severity?.color] || 'text-gray-600';
    };

    const fetchAdvisories = async () => {
        loading.value = true;
        try {
            const response = await get(
                `${props.apiUrl}/advisories?page=${currentPage.value}&per_page=${perPage.value}`,
            );

            let data = response.data.data;

            advisories.value.push(...data.data);
            currentPage.value = data.current_page;
            nextPageUrl.value = data.next_page_url;
            prevPageUrl.value = data.prev_page_url;
            lastPage.value = data.last_page;
            perPage.value = data.per_page;
            hasMore.value = nextPageUrl.value !== null;
            loading.value = false;
        } catch (error) {
            advisories.value = [];
            loading.value = false;
        }
    };
</script>
<template>
    <Page>
        <template #heading> Advisory History </template>
        <template #description> Important notices and security advisories </template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Advisories'" />
        </template>

        <div v-if="loading && advisories.length === 0" class="flex justify-center py-12">
            <LoadingSpinner label="Loading advisories..." />
        </div>

        <ResourceList v-else-if="advisories.length > 0">
            <li v-for="(advisory, index) in advisories" :key="advisory.id || index" class="px-6 py-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-semibold" :class="severityTextColor(advisory.severity)">
                            {{ advisory.title }}
                        </h3>
                        <p v-if="advisory.description" class="mt-1 text-sm text-gray-500">
                            {{ advisory.description }}
                        </p>
                        <time class="mt-1 block text-xs text-gray-400">{{ formatDate(advisory.created_at) }}</time>
                    </div>
                    <BaseBadge v-if="advisory.status" color="blue" class="shrink-0">
                        {{ advisory.status.name }}
                    </BaseBadge>
                </div>

                <template v-if="advisory.advisory_updates?.length">
                    <hr class="mt-4" />
                    <ol class="relative mt-4 border-s border-gray-200">
                        <li v-for="updateData in advisory.advisory_updates" :key="updateData.id" class="mb-8 ms-4">
                            <div
                                class="absolute w-3 h-3 bg-gray-200 rounded-lg mt-1.5 -inset-s-1.5 border border-white"
                            ></div>
                            <time class="mb-1 block text-sm font-normal leading-none text-gray-400">{{
                                formatDate(updateData.created_at)
                            }}</time>
                            <p class="mt-1 text-sm text-gray-700">{{ updateData.update }}</p>
                        </li>
                    </ol>
                </template>
            </li>

            <template #footer>
                <div v-if="loading" class="flex justify-center py-4">
                    <LoadingSpinner size="sm" />
                </div>
                <div v-if="hasMore && !loading" class="flex justify-center py-4">
                    <BaseButton color="gray" size="md" @click="loadMore"> Load More </BaseButton>
                </div>
            </template>
        </ResourceList>

        <EmptyState v-if="!loading && advisories.length === 0">
            <template #heading>There are no advisories to display.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md"> Return Home </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
