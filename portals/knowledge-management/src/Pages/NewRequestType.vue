<script setup>
import { defineProps, ref, watch, onMounted } from 'vue';
import { useRoute, onBeforeRouteUpdate } from 'vue-router';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Loading from '@/Components/Loading.vue';
import {Bars3Icon} from "@heroicons/vue/24/outline/index.js";

const route = useRoute();

const props = defineProps({
    apiUrl: {
        type: String,
        required: true,
    },
});

const loadingResults = ref(true);

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

function getData() {
    loadingResults.value = true;

    fetch(props.apiUrl + '/new-request/' + route.params.typeId)
        .then((response) => response.json())
        .then((json) => {

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
                currentCrumb="Submit Form"
                :breadcrumbs="[
                    { name: 'Help Center', route: 'home' },
                    { name: 'New Request', route: 'new-request' },
                ]"
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
                        <p class="text-white">Welcome {First Name}!</p>
                        <p class="text-white">We understand that you need some help, we're on it! Select a category below to open a new request.</p>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <h3 class="text-xl">Describe Your Issue</h3>

                <h3 class="text-xl">Additional Form Information</h3>
            </main>
        </div>
    </div>
</template>
