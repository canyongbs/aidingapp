<script setup>
import TimelineEntry from '../Components/TimelineEntry.vue';
import { onMounted, ref } from 'vue';
import { consumer } from '../Services/Consumer';

const incidents = ref([]);
const { get } = consumer();
const loading = ref(true);
const currentPage = ref(1);
const nextPageUrl = ref(null);
const prevPageUrl = ref(null);
const lastPage = ref(null);
const perPage = ref(10);


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

const severityTextColor = (severity) => {
    if (!severity || !severity.color) {
        return 'text-gray-600';
    }
    return `text-${severity.color}-600`;
};

const fetchIncidents = async () => {
    loading.value = true;
    try {
        const response = await get(`${props.apiUrl}/incidents`);

        incidents.value = response.data.data.data;
        currentPage.value = response.data.current_page;
        nextPageUrl.value = response.data.next_page_url;
        prevPageUrl.value = response.data.prev_page_url;
        lastPage.value = response.data.last_page;
        perPage.value = response.data.per_page;
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
            <hr class="my-4">
            <ol class="relative border-s border-gray-200 dark:border-gray-700">
                <li class="mb-8 ms-4" v-for="(incident, index) in data" :key="index">
                        <div class="absolute w-3 h-3 bg-gray-200 rounded-lg mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ formatDate(incident.created_at) }}</time>
                        <h3 class="text-lg font-semibold dark:text-white"
                            :class="severityTextColor(incident.severity)">{{ incident.title }}</h3>
                        <p>
                            {{ incident.description }}
                        </p>
                </li>
            </ol>
        </div>
    </div>
</template>
