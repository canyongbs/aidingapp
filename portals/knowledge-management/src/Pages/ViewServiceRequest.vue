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
    import { defineProps, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import BaseBadge from '../Components/ui/BaseBadge.vue';
    import AppLoading from '../Components/AppLoading.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Page from '../Components/Page.vue';
    import Pagination from '../Components/Pagination.vue';
    import BaseDetailSection from '../Components/ui/BaseDetailSection.vue';
    import BaseList from '../Components/ui/BaseList.vue';
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
    const authorizationError = ref(null);
    const currentPage = ref(1);
    const nextPageUrl = ref(null);
    const prevPageUrl = ref(null);
    const lastPage = ref(null);
    const totalRecords = ref(0);
    const fromRecord = ref(0);
    const toRecord = ref(0);
    const isSubmitting = ref(false);
    const acceptedMimeTypes = ref('');
    const updateFormKey = ref(0);

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
            acceptedMimeTypes.value = (response.data.acceptedMimeTypes || []).join(',');
            if (!fromPagination) {
                loadingResults.value = false;
            }
            setPagination(response.data.serviceRequestUpdates);
        });
    }

    async function submitUpdate(formValues, node) {
        try {
            isSubmitting.value = true;
            const { post } = consumer();
            const formData = new FormData();

            formData.append('description', formValues.description);
            formData.append('serviceRequestId', route.params.serviceRequestId);

            if (formValues.files && formValues.files.length) {
                formValues.files.forEach(({ file }, index) => {
                    formData.append(`files[${index}]`, file);
                });
            }

            const response = await post(props.apiUrl + '/service-request-update/store', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            serviceRequestUpdates.value = response.data.serviceRequestUpdates.data || [];
            setPagination(response.data.serviceRequestUpdates);

            updateFormKey.value++;
        } catch (error) {
            if (error.response && error.response.status === 422) {
                node.setErrors([], error.response.data.errors);
            } else if (error.response && error.response.status === 403) {
                authorizationError.value = 'You are not authorized to perform this action.';
            } else {
                console.error('Error creating update:', error);
            }
        } finally {
            isSubmitting.value = false;
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
        <template #description> View the details and status of your request </template>

        <template #breadcrumbs>
            <div class="flex flex-col gap-y-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="serviceRequest.serviceRequestNumber" />
            </div>
        </template>

        <!-- Error notices -->
        <template v-if="authorizationError">
            <div
                v-if="authorizationError"
                class="rounded-[var(--rounding-md)] bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ authorizationError }}
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
                        <BaseBadge v-if="serviceRequest.statusName" :color="serviceRequest.statusColor?.toLowerCase()">
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
            <FormKit :key="updateFormKey" type="form" :actions="false" @submit="submitUpdate">
                <FormKit
                    type="textarea"
                    name="description"
                    label="Update"
                    placeholder="Enter your update here..."
                    validation="required"
                    validation-visibility="submit"
                    :classes="{ outer: 'mb-4', inner: 'max-w-full!', input: 'w-full h-32' }"
                />
                <FormKit
                    type="file"
                    name="files"
                    label="Upload files"
                    multiple="true"
                    :accept="acceptedMimeTypes"
                    :classes="{ outer: 'mb-4' }"
                />
                <FormKit type="submit" label="Submit Update" :disabled="isSubmitting" :classes="{ input: 'mt-2' }" />
            </FormKit>
        </BaseDetailSection>

        <!-- Updates list -->
        <BaseList label="Service Request Updates" empty="No updates yet." :isEmpty="serviceRequestUpdates.length === 0">
            <div
                v-for="serviceRequestUpdate in serviceRequestUpdates"
                :key="serviceRequestUpdate.id"
                :class="['flex', serviceRequestUpdate.created_by_type === 'contact' ? 'bg-white' : 'bg-gray-50']"
            >
                <div class="w-28 shrink-0 border-r border-gray-100 px-5 py-4 text-xs leading-relaxed text-gray-400">
                    <div>{{ serviceRequestUpdate.created_at.split(' ')[0] }}</div>
                    <div>{{ serviceRequestUpdate.created_at.split(' ').slice(1).join(' ') }}</div>
                </div>
                <div class="flex-1 px-5 py-4 text-sm text-gray-700">
                    <div class="whitespace-pre-line">{{ serviceRequestUpdate.update }}</div>

                    <ul v-if="serviceRequestUpdate.media && serviceRequestUpdate.media.length" class="mt-3 space-y-1">
                        <li v-for="mediaItem in serviceRequestUpdate.media" :key="mediaItem.id">
                            <a
                                :href="mediaItem.url"
                                target="_blank"
                                download
                                class="inline-flex w-fit items-center space-x-2 rounded-lg border border-gray-100 bg-white px-2 py-1.5 text-sm transition hover:bg-gray-200"
                            >
                                <svg
                                    class="h-4 w-4 shrink-0 opacity-70"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14" />
                                </svg>
                                <span class="truncate text-gray-900">{{ mediaItem.name }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <template #footer>
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
            </template>
        </BaseList>
    </Page>
</template>
