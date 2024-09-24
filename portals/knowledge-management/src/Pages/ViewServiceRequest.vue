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
    import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
    import { defineProps, ref, watch, onMounted } from 'vue';
    import { useRoute } from 'vue-router';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import AppLoading from '../Components/AppLoading.vue';
    import { consumer } from '../Services/Consumer.js';
    import { Bars3Icon } from '@heroicons/vue/24/outline/index.js';
    import { ChevronRightIcon, XMarkIcon } from '@heroicons/vue/20/solid/index.js';
    import Article from '../Components/Article.vue';
    import Badge from '../Components/Badge.vue';

    const route = useRoute();

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
        serviceRequest: {
            type: Object,
            required: true,
        },
        serviceRequestUpdates: {
            type: Array,
            required: true,
        },
    });

    const serviceRequest = ref(null);
    const serviceRequestUpdates = ref([]);
    const loadingResults = ref(false);
    const updateMessage = ref('');

    watch(
        route.params.serviceRequestId,
        function (newRouteValue) {
            getData();
        },
        {
            immediate: true,
        },
    );

    function getData() {
        loadingResults.value = true;

        const { get } = consumer();

        get(props.apiUrl + '/service-request/' + route.params.serviceRequestId).then((response) => {
            serviceRequest.value = response.data.serviceRequestDetails;
            serviceRequestUpdates.value = response.data.serviceRequestUpdates || [];
            loadingResults.value = false;
        });
    }
    async function submitUpdate() {
        loadingResults.value = true;

        try {
            const { post } = consumer();
            const response = await post(props.apiUrl + '/service-request-update/store', {
                description: updateMessage.value,
                serviceRequestId: route.params.serviceRequestId,
            });

            // Add the new update to the list
            getData();
            updateMessage.value = ''; // Clear the textarea
        } catch (error) {
            console.error('Error creating update:', error);
        } finally {
            loadingResults.value = false;
        }
    }
</script>

<template>
    <div class="sticky top-0 z-40 flex flex-col items-center bg-gray-50">
        <div class="w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div v-if="loadingResults">
                    <AppLoading />
                </div>
                <div v-else>
                    <main class="flex flex-col gap-8">
                        <div class="flex flex-col gap-6">
                            <Breadcrumbs :currentCrumb="serviceRequest.service_request_number"></Breadcrumbs>
                            <div class="flex flex-col gap-6">
                                <h2 class="text-xl font-bold text-primary-950">Service Request Details</h2>

                                <div>
                                    <div
                                        class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white mb-4"
                                    >
                                        <!-- Service Request Details -->
                                        <div class="mb-4">
                                            <h4 class="text-base font-semibold">Title:</h4>
                                            <p class="text-gray-700">
                                                {{ serviceRequest.title }}
                                            </p>
                                        </div>

                                        <div class="mb-4">
                                            <h2 class="text-base font-semibold">Description:</h2>
                                            <p class="text-gray-700">
                                                {{ serviceRequest.description }}
                                            </p>
                                        </div>

                                        <div class="mb-4">
                                            <h2 class="text-base font-semibold">Service Request Number:</h2>
                                            <p class="text-gray-700">
                                                {{ serviceRequest.serviceRequestNumber }}
                                            </p>
                                        </div>

                                        <div class="mb-4">
                                            <h2 class="text-base font-semibold">Status:</h2>
                                            <p class="text-gray-700">
                                                <Badge
                                                    :value="serviceRequest.statusName"
                                                    :class="`inline-flex px-2 py-1 rounded-full`"
                                                    :color="serviceRequest.statusColor"
                                                />
                                            </p>
                                        </div>

                                        <div class="mb-4">
                                            <h2 class="text-base font-semibold">Type:</h2>
                                            <p class="text-gray-700">
                                                {{ serviceRequest.typeName }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Add New Update Form -->
                                    <div
                                        class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white"
                                    >
                                        <h2 class="text-xl font-bold mb-4">New Service Request Update</h2>

                                        <form @submit.prevent="submitUpdate">
                                            <div class="mb-4">
                                                <textarea
                                                    v-model="updateMessage"
                                                    class="w-full h-32 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500"
                                                    placeholder="Enter your update here..."
                                                    required
                                                ></textarea>
                                            </div>
                                            <button
                                                type="submit"
                                                class="w-auto py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-500 transition duration-200"
                                            >
                                                Submit Update
                                            </button>
                                        </form>
                                    </div>
                                    <!-- Service Request Updates Section -->
                                    <div
                                        class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white mb-4"
                                    >
                                        <h2 class="text-xl font-bold mb-4">Service Request Updates</h2>

                                        <div id="updates-list">
                                            <!-- Updates will be dynamically inserted here -->

                                            <div
                                                :class="
                                                    serviceRequestUpdate.internal
                                                        ? 'mb-4 p-4 bg-gray-50 border border-gray-200 rounded'
                                                        : 'mb-4 p-4 border border-blue-200 rounded bg-gradient-to-br from-primary-500 to-primary-800 text-white'
                                                "
                                                v-for="serviceRequestUpdate in serviceRequestUpdates"
                                                :key="serviceRequestUpdate.id"
                                            >
                                                <p :class="serviceRequestUpdate.internal ? 'text-gray-700' : ''">
                                                    {{ serviceRequestUpdate.update }}
                                                </p>
                                                <span
                                                    :class="
                                                        serviceRequestUpdate.internal
                                                            ? 'text-sm text-gray-500'
                                                            : 'text-sm'
                                                    "
                                                >
                                                    {{ serviceRequestUpdate.created_at }}
                                                </span>
                                            </div>

                                            <!-- More updates can be added here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
