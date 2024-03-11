<script setup>
import axios from '@/Globals/Axios.js';
import { defineProps, ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Loading from '@/Components/Loading.vue';
import { Bars3Icon } from '@heroicons/vue/24/outline/index.js';
import { useAuthStore } from '@/Stores/auth.js';
import { useTokenStore } from '@/Stores/token.js';
import { consumer } from '@/Services/Consumer.js';

const route = useRoute();

const props = defineProps({
    apiUrl: {
        type: String,
        required: true,
    },
});

const loadingResults = ref(true);
const types = ref(null);
const user = ref(null);

watch(
    route,
    function (newRouteValue) {
        getData();
    },
    {
        immediate: true,
    },
);

onMounted(function () {
    getData();
});

async function getData() {
    loadingResults.value = true;

    const { getUser } = useAuthStore();

    await getUser().then((authUser) => {
        user.value = authUser;
    });

    const { get } = consumer();

    get(props.apiUrl + '/service-request-type/select').then((response) => {
        types.value = response.data.types;
        loadingResults.value = false;
    });
}
</script>

<template>
    <div>
        <div v-if="loadingResults">
            <Loading />
        </div>
        <div v-else>
            <Breadcrumbs
                currentCrumb="New Request"
                :breadcrumbs="[{ name: 'Help Center', route: 'home' }]"
            ></Breadcrumbs>

            <div
                class="sticky top-0 z-40 flex flex-col items-center border-b border-gray-100 bg-white px-4 py-4 shadow-sm sm:px-6 lg:px-8"
            >
                <button class="w-full p-2.5 lg:hidden" type="button" v-on:click="showMobileMenu = !showMobileMenu">
                    <span class="sr-only">Open sidebar</span>
                    <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
                </button>

                <div class="flex h-full w-full flex-col rounded bg-primary-700 px-12 py-4">
                    <div class="flex flex-col text-left">
                        <h3 class="text-3xl text-white">Help Center</h3>
                        <p class="text-white">Welcome {{ user.first_name }}!</p>
                        <p class="text-white">
                            We understand that you need some help, we're on it! Please complete the form below.
                        </p>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <h3 class="text-xl">Select Category</h3>

                <div class="mt-4 grid gap-y-4">
                    <div v-for="type in types" :key="type.id" class="group relative bg-white p-6 rounded shadow">
                        <div class="flex items-center gap-x-3">
                            <span
                                v-if="type.icon"
                                v-html="type.icon"
                                class="pointer-events-none text-primary-600 dark:text-primary-400"
                                aria-hidden="true"
                            >
                            </span>
                            <div class="w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">
                                    <router-link
                                        :to="{ name: 'create-service-request-from-type', params: { typeId: type.id } }"
                                    >
                                        <span class="absolute inset-0" aria-hidden="true" />
                                        {{ type.name }}
                                    </router-link>
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    {{ type.description }}
                                </p>
                            </div>
                            <span
                                class="pointer-events-none text-gray-300 group-hover:text-primary-600 group-hover:dark:text-primary-400"
                                aria-hidden="true"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-6 h-6"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m8.25 4.5 7.5 7.5-7.5 7.5"
                                    />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
