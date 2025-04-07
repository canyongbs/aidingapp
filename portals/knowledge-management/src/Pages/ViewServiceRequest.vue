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
    import { defineProps, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import AppLoading from '../Components/AppLoading.vue';
    import Badge from '../Components/Badge.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Pagination from '../Components/Pagination.vue';
    import { consumer } from '../Services/Consumer.js';

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
        directionEnums: {
            type: Array,
            required: true,
        },
    });

    const serviceRequest = ref(null);
    const serviceRequestUpdates = ref([]);
    const directionEnums = ref([]);
    const loadingResults = ref(false);
    const updateMessage = ref('');
    const validationErrors = ref({});
    const authorizationError = ref(null);
    const currentPage = ref(1);
    const nextPageUrl = ref(null);
    const prevPageUrl = ref(null);
    const lastPage = ref(null);
    const totalRecords = ref(0);
    const fromRecord = ref(0);
    const toRecord = ref(0);
    const disableSubmitBtn = ref(false);

    const setPagination = (pagination) => {
        currentPage.value = pagination.current_page;
        prevPageUrl.value = pagination.prev_page_url;
        nextPageUrl.value = pagination.next_page_url;
        lastPage.value = pagination.last_page;
        totalRecords.value = pagination.total;
        fromRecord.value = pagination.from;
        toRecord.value = pagination.to;
    };

    watch(
        route.params.serviceRequestId,
        function (newRouteValue) {
            getData();
        },
        {
            immediate: true,
        },
    );

    function getData(page = 1, fromPagination = false) {
        if (!fromPagination) {
            loadingResults.value = true;
        }

        const { get } = consumer();

        get(props.apiUrl + '/service-request/' + route.params.serviceRequestId, { page: page }).then((response) => {
            serviceRequest.value = response.data.serviceRequestDetails;
            serviceRequestUpdates.value = response.data.serviceRequestUpdates.data || [];
            directionEnums.value = response.data.directionEnums || [];
            if (!fromPagination) {
                loadingResults.value = false;
            }
            setPagination(response.data.serviceRequestUpdates);
        });
    }
    async function submitUpdate() {
        try {
            disableSubmitBtn.value = true;
            const { post } = consumer();
            const response = await post(props.apiUrl + '/service-request-update/store', {
                description: updateMessage.value,
                serviceRequestId: route.params.serviceRequestId,
            });
            serviceRequestUpdates.value = response.data.serviceRequestUpdates.data || [];
            setPagination(response.data.serviceRequestUpdates);
            updateMessage.value = ''; // Clear the textarea
        } catch (error) {
            if (error.response && error.response.status === 422) {
                // 422 Unprocessable Entity, which means validation error
                validationErrors.value = error.response.data.errors; // Assign validation errors
            } else if (error.response && error.response.status === 403) {
                // Handle authorization errors
                authorizationError.value = 'You are not authorized to perform this action.';
            } else {
                console.error('Error creating update:', error);
            }
        } finally {
            disableSubmitBtn.value = false;
        }
    }

    const fetchNextPage = () => {
        currentPage.value = currentPage.value !== lastPage.value ? currentPage.value + 1 : lastPage.value;
        getData(currentPage.value, true);
    };

    const fetchPreviousPage = () => {
        currentPage.value = currentPage.value !== 1 ? currentPage.value - 1 : 1;
        getData(currentPage.value, true);
    };

    const fetchPage = (page) => {
        currentPage.value = page;
        getData(currentPage.value, true);
    };

    const breadcrumbs = [
        {
            name: 'Services',
            route: 'services',
            params: null,
        },
    ];
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
                            <Breadcrumbs
                                :breadcrumbs="breadcrumbs"
                                :currentCrumb="serviceRequest.serviceRequestNumber"
                            ></Breadcrumbs>
                            <div class="flex flex-col gap-6">
                                <div v-if="authorizationError" class="text-red-500 text-sm">
                                    {{ authorizationError }}
                                </div>
                                <div v-if="validationErrors.serviceRequestId" class="text-red-500 text-sm">
                                    <p v-for="error in validationErrors.serviceRequestId" :key="error">
                                        {{ error }}
                                    </p>
                                </div>
                                <h2 class="text-xl font-bold text-brand-950">Service Request Details</h2>

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
                                                    v-if="serviceRequest.statusName"
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
                                        class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white mb-4"
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
                                                <div v-if="validationErrors.description" class="text-red-500 text-sm">
                                                    <p v-for="error in validationErrors.description" :key="error">
                                                        {{ error }}
                                                    </p>
                                                </div>
                                            </div>
                                            <button
                                                type="submit"
                                                class="w-auto py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-500 transition duration-200"
                                                :disabled="disableSubmitBtn"
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
                                                    serviceRequestUpdate.direction == directionEnums.Inbound
                                                        ? 'mb-4 p-4 bg-gray-50 border border-gray-200 rounded'
                                                        : 'mb-4 p-4 border border-blue-200 rounded bg-gradient-to-br from-brand-500 to-brand-800 text-white'
                                                "
                                                v-for="serviceRequestUpdate in serviceRequestUpdates"
                                                :key="serviceRequestUpdate.id"
                                            >
                                                <p
                                                    :class="
                                                        serviceRequestUpdate.direction == directionEnums.Inbound
                                                            ? 'text-gray-700'
                                                            : ''
                                                    "
                                                >
                                                    {{ serviceRequestUpdate.update }}
                                                </p>
                                                <span
                                                    :class="
                                                        serviceRequestUpdate.direction == directionEnums.Inbound
                                                            ? 'text-sm text-gray-500'
                                                            : 'text-sm'
                                                    "
                                                >
                                                    {{ serviceRequestUpdate.created_at }}
                                                </span>
                                            </div>
                                            <Pagination
                                                :currentPage="currentPage"
                                                :lastPage="lastPage"
                                                :fromArticle="fromRecord"
                                                :toArticle="toRecord"
                                                :totalArticles="totalRecords"
                                                @fetchNextPage="fetchNextPage"
                                                @fetchPreviousPage="fetchPreviousPage"
                                                @fetchPage="fetchPage"
                                            />
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
