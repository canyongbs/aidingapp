<script setup>
    import { XMarkIcon } from '@heroicons/vue/24/outline';
    import { computed, onMounted, ref } from 'vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Pagination from '../Components/Pagination.vue';
    import ServiceMonitorAlert from '../Components/ServiceMonitorAlert.vue';
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

    const hasIssues = computed(() => result.value.some((service) => service.status !== 'ok'));

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
                result.value = response.data;
                setPagination(response.meta);
                loading.value = false;
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
    <main class="px-6 bg-gray-50 min-h-screen">
        <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <Breadcrumbs :currentCrumb="'Status'"></Breadcrumbs>
            <div v-if="!loading">
                <ServiceMonitorAlert
                    v-if="result.length > 0"
                    :hasIssue="hasIssues"
                    :title="systemTitle"
                    :message="systemMessage"
                />
            </div>
            <div v-else class="p-5 border border-gray-200 bg-white rounded-md animate-pulse w-100">
                <div class="flex items-center space-x-3">
                    <div class="h-5 w-5 bg-gray-300 rounded-full"></div>
                    <div class="h-4 bg-gray-300 rounded w-48"></div>
                </div>
                <div class="mt-2 h-3 bg-gray-200 rounded w-3/4"></div>
                <div class="mt-1 h-3 bg-gray-200 rounded w-2/3"></div>
            </div>

            <template v-if="!loading">
                <div v-if="result.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(service, index) in result" :key="index">
                        <ServiceMonitorCard :name="service.name" :status="service.status" :message="service.message" />
                    </div>
                </div>
                <div v-else class="p-3 flex items-start gap-2">
                    <XMarkIcon class="h-5 w-5 text-gray-400" />

                    <p class="text-gray-600 text-sm font-medium">No service monitors found.</p>
                </div>

                <Pagination
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="n in 6"
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
        </div>
    </main>
</template>
