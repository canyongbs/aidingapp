<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    import { defineProps, ref, watch, onMounted } from 'vue';
    import { useRoute } from 'vue-router';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import { Bars3Icon } from '@heroicons/vue/24/outline/index.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { consumer } from '../Services/Consumer.js';
    import AppLoading from '../Components/AppLoading.vue';

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
            <AppLoading />
        </div>
        <div v-else>
            <div class="sticky top-0 z-40 flex flex-col items-center bg-gray-50">
                <button class="w-full p-3 lg:hidden" type="button" v-on:click="showMobileMenu = !showMobileMenu">
                    <span class="sr-only">Open sidebar</span>

                    <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
                </button>

                <div class="bg-gradient-to-br from-primary-500 to-primary-800 w-full px-6">
                    <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                        <div class="flex flex-col gap-y-1 text-left">
                            <h3 class="text-3xl text-white">Help Center</h3>
                            <p class="text-white">Welcome {{ user.first_name }}!</p>
                            <p class="text-white">
                                We understand that you need some help, we're on it! Please complete the form below.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <Breadcrumbs
                class="px-6 py-8"
                currentCrumb="New Request"
                :breadcrumbs="[{ name: 'Help Center', route: 'home' }]"
            ></Breadcrumbs>

            <main class="px-6">
                <h3 class="text-xl">Select Category</h3>

                <div class="my-4 grid gap-y-4">
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
