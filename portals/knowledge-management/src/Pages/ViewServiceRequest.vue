<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
    import { ArrowLeftIcon } from '@heroicons/vue/20/solid';
    import { defineProps, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import AppLoading from '../Components/AppLoading.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Page from '../Components/Page.vue';
    import Pagination from '../Components/Pagination.vue';
    import BaseBadge from '../Components/ui/BaseBadge.vue';
    import BaseButton from '../Components/ui/BaseButton.vue';
    import BaseDetailSection from '../Components/ui/BaseDetailSection.vue';
    import BaseTable from '../Components/ui/BaseTable.vue';
    import BaseTableBody from '../Components/ui/BaseTableBody.vue';
    import BaseTableCell from '../Components/ui/BaseTableCell.vue';
    import BaseTableHeader from '../Components/ui/BaseTableHeader.vue';
    import BaseTableHeaderCell from '../Components/ui/BaseTableHeaderCell.vue';
    import BaseTableRow from '../Components/ui/BaseTableRow.vue';
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
    });

    const serviceRequest = ref(null);
    const serviceRequestUpdates = ref([]);
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
            name: 'Service',
            route: 'service',
            params: null,
        },
    ];
</script>

<template>
    <div v-if="loadingResults">
        <AppLoading />
    </div>

    <Page v-else>
        <template #heading>Service Request Details</template>

        <template #breadcrumbs>
            <div class="flex flex-col gap-y-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="serviceRequest.serviceRequestNumber" />
                <router-link
                    :to="{ name: 'service' }"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    Back to My Service Requests
                </router-link>
            </div>
        </template>

        <template #rawContent>
            <!-- Error notices -->
            <template v-if="authorizationError || validationErrors.serviceRequestId">
                <div
                    v-if="authorizationError"
                    class="rounded-[var(--rounding-md)] bg-red-50 px-4 py-3 text-sm text-red-700"
                >
                    {{ authorizationError }}
                </div>
                <div
                    v-if="validationErrors.serviceRequestId"
                    class="rounded-[var(--rounding-md)] bg-red-50 px-4 py-3 text-sm text-red-700"
                >
                    <p v-for="error in validationErrors.serviceRequestId" :key="error">{{ error }}</p>
                </div>
            </template>

            <!-- Summary table -->
            <BaseTable>
                <BaseTableHeader>
                    <tr>
                        <BaseTableHeaderCell>Service Request #</BaseTableHeaderCell>
                        <BaseTableHeaderCell>Type</BaseTableHeaderCell>
                        <BaseTableHeaderCell>Status</BaseTableHeaderCell>
                        <BaseTableHeaderCell>Date Opened</BaseTableHeaderCell>
                        <BaseTableHeaderCell>Last Updated</BaseTableHeaderCell>
                    </tr>
                </BaseTableHeader>
                <BaseTableBody>
                    <BaseTableRow>
                        <BaseTableCell class="whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ serviceRequest.serviceRequestNumber }}
                        </BaseTableCell>
                        <BaseTableCell class="text-sm text-gray-600">
                            {{ serviceRequest.typeName ?? '—' }}
                        </BaseTableCell>
                        <BaseTableCell>
                            <BaseBadge
                                v-if="serviceRequest.statusName"
                                :color="serviceRequest.statusColor?.toLowerCase()"
                            >
                                {{ serviceRequest.statusName }}
                            </BaseBadge>
                            <span v-else class="text-sm text-gray-400">—</span>
                        </BaseTableCell>
                        <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                            {{ serviceRequest.dateOpened ?? '—' }}
                        </BaseTableCell>
                        <BaseTableCell class="whitespace-nowrap text-sm text-gray-600">
                            {{ serviceRequest.lastUpdated ?? '—' }}
                        </BaseTableCell>
                    </BaseTableRow>
                </BaseTableBody>
            </BaseTable>

            <!-- Title -->
            <BaseDetailSection label="Title">
                <p class="text-sm text-gray-900">{{ serviceRequest.title }}</p>
            </BaseDetailSection>

            <!-- Description -->
            <BaseDetailSection label="Description">
                <p class="whitespace-pre-line text-sm text-gray-700">{{ serviceRequest.description }}</p>
            </BaseDetailSection>

            <!-- New update form -->
            <BaseDetailSection label="New Service Request Update">
                <form @submit.prevent="submitUpdate">
                    <textarea
                        v-model="updateMessage"
                        rows="5"
                        class="w-full resize-y rounded-[var(--rounding-md)] border border-gray-300 p-3 text-sm focus:border-[rgb(var(--primary-500))] focus:outline-hidden focus:ring-2 focus:ring-[rgb(var(--primary-500))]"
                        placeholder="Enter your update here..."
                        required
                    ></textarea>
                    <div v-if="validationErrors.description" class="mt-1 text-sm text-red-500">
                        <p v-for="error in validationErrors.description" :key="error">{{ error }}</p>
                    </div>
                    <div class="mt-3">
                        <BaseButton type="submit" variant="primary" size="md" :loading="disableSubmitBtn">
                            Submit Update
                        </BaseButton>
                    </div>
                </form>
            </BaseDetailSection>

            <!-- Updates list -->
            <div class="overflow-hidden rounded-[var(--rounding-lg)] border border-gray-200 bg-white shadow-xs">
                <div class="border-b border-gray-100 px-5 py-3">
                    <p class="text-xs font-semibold text-gray-500">Service Request Updates</p>
                </div>

                <div v-if="serviceRequestUpdates.length === 0" class="px-5 py-8 text-center text-sm text-gray-400">
                    No updates yet.
                </div>

                <div v-else class="divide-y divide-gray-100">
                    <div
                        v-for="serviceRequestUpdate in serviceRequestUpdates"
                        :key="serviceRequestUpdate.id"
                        :class="[
                            'flex',
                            serviceRequestUpdate.created_by_type === 'contact' ? 'bg-white' : 'bg-gray-50',
                        ]"
                    >
                        <div
                            class="w-28 shrink-0 border-r border-gray-100 px-5 py-4 text-xs leading-relaxed text-gray-400"
                        >
                            <div>{{ serviceRequestUpdate.created_at.split(' ')[0] }}</div>
                            <div>{{ serviceRequestUpdate.created_at.split(' ').slice(1).join(' ') }}</div>
                        </div>
                        <div class="flex-1 px-5 py-4 text-sm text-gray-700">
                            {{ serviceRequestUpdate.update }}
                        </div>
                    </div>
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
            </div>
        </template>
    </Page>
</template>
