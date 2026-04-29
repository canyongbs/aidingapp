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
    import BaseInputError from '../Components/ui/BaseInputError.vue';
    import BaseList from '../Components/ui/BaseList.vue';
    import BaseTable from '../Components/ui/BaseTable.vue';
    import BaseTableBody from '../Components/ui/BaseTableBody.vue';
    import BaseTableCell from '../Components/ui/BaseTableCell.vue';
    import BaseTableHeader from '../Components/ui/BaseTableHeader.vue';
    import BaseTableHeaderCell from '../Components/ui/BaseTableHeaderCell.vue';
    import BaseTableRow from '../Components/ui/BaseTableRow.vue';
    import BaseTextarea from '../Components/ui/BaseTextarea.vue';
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
    const files = ref([]);
    const fileInput = ref(null);
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
    const isDragging = ref(false);

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

    const addFiles = (newFiles) => {
        const existing = new Set(files.value.map((f) => `${f.name}-${f.size}-${f.lastModified}`));

        const duplicates = [];
        const unique = [];

        newFiles.forEach((file) => {
            const key = `${file.name}-${file.size}-${file.lastModified}`;

            if (existing.has(key)) {
                duplicates.push(file);
            } else {
                unique.push(file);
                existing.add(key);
            }
        });

        files.value = [...files.value, ...unique];

        if (duplicates.length) {
            validationErrors.value.files = duplicates.map((file) => `${file.name} has already been added`);
        } else {
            delete validationErrors.value.files;
        }
    };

    const removeFile = (index) => {
        files.value.splice(index, 1);

        if (!files.value.length) {
            delete validationErrors.value.files;
        }
    };

    const handleFiles = (event) => {
        const selected = Array.from(event.target.files);

        addFiles(selected);

        event.target.value = '';
    };

    const handleDrop = (event) => {
        event.preventDefault();
        isDragging.value = false;

        const droppedFiles = Array.from(event.dataTransfer.files);
        addFiles(droppedFiles);
    };

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
            const formData = new FormData();

            formData.append('description', updateMessage.value);
            formData.append('serviceRequestId', route.params.serviceRequestId);
            files.value.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });

            const response = await post(props.apiUrl + '/service-request-update/store', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            serviceRequestUpdates.value = response.data.serviceRequestUpdates.data || [];
            setPagination(response.data.serviceRequestUpdates);
            updateMessage.value = ''; // Clear the textarea
            files.value = [];
            if (fileInput.value) {
                fileInput.value.value = null;
            }
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
                    <BaseTextarea v-model="updateMessage" :rows="5" placeholder="Enter your update here..." required />
                    <div class="my-4">
                        <div class="my-4">
                            <label class="block font-bold mb-2"> Upload files </label>

                            <div
                                class="rounded-lg p-6 text-center transition"
                                :class="isDragging ? 'bg-taupe-300' : 'bg-taupe-100'"
                                @click="$refs.fileInput.click()"
                                @dragover.prevent="isDragging = true"
                                @dragenter.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop"
                            >
                                <input ref="fileInput" type="file" multiple class="hidden" @change="handleFiles" />

                                <div class="text-taupe-600">
                                    <span>Drop files here or </span>
                                    <span class="underline hover:cursor-pointer"> Browse </span>
                                </div>

                                <ul v-if="files.length" class="mt-4 space-y-2">
                                    <li
                                        v-for="(file, index) in files"
                                        :key="index"
                                        class="flex items-center justify-between bg-neutral-700 rounded-lg px-3 py-2 shadow-sm"
                                    >
                                        <div class="flex flex-col leading-tight items-start">
                                            <span class="block text-sm text-white truncate">
                                                {{ file.name }}
                                            </span>
                                            <span class="block text-xs text-neutral-400">
                                                {{ (file.size / 1024).toFixed(1) }} KB
                                            </span>
                                        </div>

                                        <button
                                            type="button"
                                            class="ml-3 flex items-center justify-center w-7 h-7 text-neutral-400 hover:text-white bg-neutral-900 hover:bg-neutral-600 transition shrink-0"
                                            style="border-radius: 9999px"
                                            @click.stop="removeFile(index)"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2.5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M6 6l12 12M6 18L18 6"
                                                />
                                            </svg>
                                        </button>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    <BaseInputError :errors="validationErrors.description ?? []" />
                    <div class="mt-3">
                        <BaseButton type="submit" variant="primary" size="md" :loading="disableSubmitBtn">
                            Submit Update
                        </BaseButton>
                    </div>
                        <div v-if="validationErrors.files" class="text-red-500 text-sm">
                            <p v-for="error in validationErrors.files" :key="error">
                                {{ error }}
                            </p>
                        </div>
                    </div>
                    <BaseButton type="submit" variant="primary" size="md" :loading="disableSubmitBtn">
                        Submit Update
                    </BaseButton>
                </form>
            </BaseDetailSection>

            <!-- Updates list -->
            <BaseList
                label="Service Request Updates"
                empty="No updates yet."
                :isEmpty="serviceRequestUpdates.length === 0"
            >
                <div
                    v-for="serviceRequestUpdate in serviceRequestUpdates"
                    :key="serviceRequestUpdate.id"
                    :class="['flex', serviceRequestUpdate.created_by_type === 'contact' ? 'bg-white' : 'bg-gray-50']"
                >
                    <div class="w-28 shrink-0 border-r border-gray-100 px-5 py-4 text-xs leading-relaxed text-gray-400">
                        <div>{{ serviceRequestUpdate.created_at.split(' ')[0] }}</div>
                        <div>{{ serviceRequestUpdate.created_at.split(' ').slice(1).join(' ') }}</div>
                    </div>
                    <div class="flex-1 whitespace-pre-line px-5 py-4 text-sm text-gray-700">
                        {{ serviceRequestUpdate.update }}
                    </div>

                        <div v-if="serviceRequestUpdate.media && serviceRequestUpdate.media.length" class="mt-3">
                            <ul class="my-3">
                                <li v-for="mediaItem in serviceRequestUpdate.media" :key="mediaItem.id">
                                    <a
                                        :href="mediaItem.url"
                                        target="_blank"
                                        download
                                        class="text-sm flex items-center justify-between rounded-lg border border-gray-100 bg-white px-3 py-2 transition hover:bg-gray-200"
                                    >
                                        <div class="flex items-center space-x-2 overflow-hidden">
                                            <svg
                                                class="w-4 h-4 shrink-0 opacity-70 group-hover:opacity-100"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                            >
                                                <path d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14" />
                                            </svg>

                                            <span class="text-sm truncate text-gray-900">
                                                {{ mediaItem.name }}
                                            </span>
                                        </div>
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
        </template>
    </Page>
</template>
