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
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import EmptyState from '../Components/EmptyState.vue';
    import Loader from '../Components/Loader.vue';
    import Page from '../Components/Page.vue';
    import BaseButton from '../Components/ui/BaseButton.vue';
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
            const response = await get(`${props.apiUrl}/advisories?page=${currentPage.value}&per_page=${perPage.value}`);

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

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Advisories'" />
        </template>

        <div class="mb-6 bg-white shadow-xs rounded-lg p-4" v-for="(advisory, index) in advisories" :key="index">
            <time class="mb-1 text-lg font-semibold leading-none text-black">{{
                formatDate(advisory.created_at)
            }}</time>
            <h3 class="text-lg font-semibold" :class="severityTextColor(advisory.severity)">
                {{ advisory.title }}
            </h3>
            <p class="text-sm text-gray-500">
                {{ advisory.description }}
            </p>
            <span
                class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm mb-5"
                v-if="advisory.status"
                >{{ advisory.status.name }}</span
            >
            <hr class="my-4" v-if="advisory.advisory_updates?.length" />
            <ol class="relative border-s border-gray-200" v-if="advisory.advisory_updates?.length">
                <li class="mb-8 ms-4" v-for="updateData in advisory.advisory_updates" :key="updateData.id">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-lg mt-1.5 -inset-s-1.5 border border-white"></div>

                    <time class="mb-1 text-sm font-normal leading-none text-gray-400">{{
                        formatDate(updateData.created_at)
                    }}</time>
                    <p class="text-sm text-gray-700 mt-1">{{ updateData.update }}</p>
                </li>
            </ol>
        </div>
        <Loader :loading="loading" />
        <div class="flex justify-center mt-6" v-if="hasMore && !loading">
            <BaseButton variant="primary" size="md" @click="loadMore"> Load More </BaseButton>
        </div>

        <EmptyState v-if="!loading && advisories.length === 0">
            <template #heading>There are no advisories to display.</template>
            <template #actions>
                <BaseButton as="router-link" :to="{ name: 'home' }" variant="primary" size="md">
                    Return Home
                </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
