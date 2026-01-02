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
    import { computed, onMounted, ref } from 'vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import EmptyState from '../Components/EmptyState.vue';
    import Page from '../Components/Page.vue';
    import Pagination from '../Components/Pagination.vue';
    import ServiceMonitorCard from '../Components/ServiceMonitorCard.vue';
    import { consumer } from '../Services/Consumer.js';

    const emit = defineEmits(['fetchNextPage', 'fetchPreviousPage', 'fetchPage', 'change-filter']);

    const result = ref([]);
    const { get } = consumer();
    const loading = ref(true);

    const currentPage = ref(1);
    const nextPageUrl = ref(null);
    const prevPageUrl = ref(null);
    const lastPage = ref(null);
    const totalArticles = ref(0);
    const fromArticle = ref(0);
    const toArticle = ref(0);

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    const okTitle = 'All systems operational';
    const okMessage =
        'All systems are functioning seamlessly, with no disruptions or downtime reported. Every component, from critical infrastructure to auxiliary services is running at full capacity, ensuring optimal performance and reliability.';

    const issueTitle = 'Some systems are experiencing issues';
    const issueMessage = 'One or more services are currently experiencing disruptions or downtime.';

    const hasIssues = computed(() =>
        result.value.some((serviceMonitor) => serviceMonitor.latest_history?.succeeded === false),
    );

    const hasAnyHistory = computed(() => result.value.some((serviceMonitor) => serviceMonitor.latest_history !== null));

    const systemTitle = computed(() => (hasIssues.value ? issueTitle : okTitle));
    const systemMessage = computed(() => (hasIssues.value ? issueMessage : okMessage));

    const setPagination = (pagination) => {
        currentPage.value = pagination.current_page;
        prevPageUrl.value = pagination.prev_page_url;
        nextPageUrl.value = pagination.next_page_url;
        lastPage.value = pagination.last_page;
        totalArticles.value = pagination.total;
        fromArticle.value = pagination.from;
        toArticle.value = pagination.to;
    };

    const fetchNextPage = () => {
        loading.value = true;
        currentPage.value = currentPage.value !== lastPage.value ? currentPage.value + 1 : lastPage.value;
        getServiceMonitors(currentPage.value);
    };

    const fetchPreviousPage = () => {
        loading.value = true;
        currentPage.value = currentPage.value !== 1 ? currentPage.value - 1 : 1;
        getServiceMonitors(currentPage.value);
    };

    const fetchPage = (page) => {
        loading.value = true;
        getServiceMonitors(page);
    };

    async function fetchData(page = 1) {
        const response = await get(`${props.apiUrl}/status?page=${page}`);

        if (response.error) {
            throw new Error(response.error);
        }

        return response.data;
    }

    async function getServiceMonitors(page = 1) {
        await fetchData(page)
            .then((response) => {
                loading.value = false;
                result.value = response.data;
                setPagination(response.meta);
            })
            .catch((error) => {
                console.error('Error fetching service monitors:', error);
            });
    }

    onMounted(async () => {
        getServiceMonitors();
    });
</script>

<template>
    <Page>
        <template #heading> Status </template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Status'" />
        </template>

        <template v-if="!loading">
            <div v-if="result.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(serviceMonitor, index) in result" :key="index">
                    <ServiceMonitorCard
                        :name="serviceMonitor.name"
                        :status="serviceMonitor.latest_history?.succeeded ?? true"
                        :message="
                            serviceMonitor.latest_history?.status_message ??
                            'No known issues (monitoring not yet started).'
                        "
                    />
                </div>
            </div>

            <EmptyState v-else>
                <template #heading>There are no service monitors to display.</template>
                <template #actions>
                    <router-link
                        :to="{ name: 'home' }"
                        class="inline-block px-4 py-2 text-white bg-gradient-to-br from-brand-500 to-brand-800 rounded"
                    >
                        Return Home
                    </router-link>
                </template>
            </EmptyState>

            <Pagination
                class="mt-3"
                v-if="result.length > 0"
                :currentPage="currentPage"
                :lastPage="lastPage"
                :fromArticle="fromArticle"
                :toArticle="toArticle"
                :totalArticles="totalArticles"
                @fetchNextPage="fetchNextPage"
                @fetchPreviousPage="fetchPreviousPage"
                @fetchPage="fetchPage"
            />
        </template>

        <template v-else>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-3">
                <div
                    v-for="n in 15"
                    :key="`service-monitor-skeleton-${n}`"
                    class="flex items-center justify-between p-5 bg-white border border-gray-200 rounded-lg shadow animate-pulse"
                >
                    <div class="flex items-center">
                        <div class="h-7 w-7 bg-gray-300 rounded-full"></div>

                        <div class="ml-4">
                            <div class="h-4 bg-gray-300 rounded w-24 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-32"></div>
                        </div>
                    </div>

                    <div class="h-5 w-5 bg-gray-300 rounded-full"></div>
                </div>
            </div>
        </template>
    </Page>
</template>
