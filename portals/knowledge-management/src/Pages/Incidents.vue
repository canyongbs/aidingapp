<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    import Loader from '../Components/Loader.vue';
    import { consumer } from '../Services/Consumer';

    const incidents = ref({});
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
        fetchIncidents();
    });

    const formatDate = (date) => {
        const d = new Date(date);
        return d.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
        });
    };

    const loadMore = () => {
        if (nextPageUrl.value) {
            currentPage.value++;
            fetchIncidents();
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

    const fetchIncidents = async () => {
        loading.value = true;
        try {
            const response = await get(`${props.apiUrl}/incidents?page=${currentPage.value}&per_page=${perPage.value}`);

            let data = response.data.data;
            for (const [monthYear, newIncidents] of Object.entries(data.data)) {
                if (!incidents.value[monthYear]) {
                    incidents.value[monthYear] = [];
                }
                incidents.value[monthYear].push(...newIncidents);
            }
            currentPage.value = data.current_page;
            nextPageUrl.value = data.next_page_url;
            prevPageUrl.value = data.prev_page_url;
            lastPage.value = data.last_page;
            perPage.value = data.per_page;
            hasMore.value = nextPageUrl.value !== null;
            loading.value = false;
        } catch (error) {
            incidents.value = [];
            loading.value = false;
        }
    };
</script>
<template>
    <div class="p-6 max-w-6xl mx-auto">
        <h1 class="text-2xl font-semibold mb-6">Incident History</h1>
        <div class="mb-6 bg-white shadow rounded-lg p-4" v-for="(data, index) in incidents" :key="index">
            <h2 class="text-lg font-medium mb-4">{{ index }}</h2>
            <hr class="my-4" />
            <ol class="relative border-s border-gray-200 dark:border-gray-700">
                <li class="mb-8 ms-4" v-for="incident in data" :key="incident.id">
                    <div
                        class="absolute w-3 h-3 bg-gray-200 rounded-lg mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"
                    ></div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{
                        formatDate(incident.created_at)
                    }}</time>
                    <h3
                        v-if="incident.severity"
                        class="text-lg font-semibold dark:text-white"
                        :class="severityTextColor(incident.severity)"
                    >
                        {{ incident.title }}
                    </h3>
                    <p>
                        {{ incident.description }}
                    </p>
                </li>
            </ol>
        </div>
        <Loader :loading="loading" />
        <div class="flex justify-center mt-6" v-if="hasMore && !loading">
            <button
                type="button"
                @click="loadMore"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
            >
                Load More
            </button>
        </div>
    </div>
</template>
