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
    import BaseButton from '@common/BaseButton.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import PageCard from '@common/portal/PageCard.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import { InformationCircleIcon } from '@heroicons/vue/20/solid';
    import { useQuery } from '@pinia/colada';
    import { computed, ref, watch } from 'vue';
    import PipelineSelect from '../Components/Projects/PipelineSelect.vue';
    import ProjectPipelineTable from '../Components/Projects/ProjectPipelineTable.vue';
    import { apiGet } from '../Services/api.js';
    import { useProjectData } from './loaders.js';

    const { data: initialEnvelope } = useProjectData();

    const project = computed(() => initialEnvelope.value?.data ?? null);
    const pipelines = computed(() => project.value?.pipelines ?? []);
    const initialPipelineId = computed(
        () => pipelines.value.find((pipeline) => Array.isArray(pipeline.groups))?.id ?? pipelines.value[0]?.id ?? null,
    );

    const selectedPipelineId = ref(null);
    const currentPage = ref(1);

    watch(
        pipelines,
        (value) => {
            if (!value.some((pipeline) => pipeline.id === selectedPipelineId.value)) {
                selectedPipelineId.value = value[0]?.id ?? null;
            }
        },
        { immediate: true },
    );

    watch(selectedPipelineId, (value, previousValue) => {
        if (previousValue !== null && value !== previousValue) {
            currentPage.value = 1;
        }
    });

    const pageQuery = useQuery({
        key: () => ['knowledge-management', 'project', project.value?.id, selectedPipelineId.value, currentPage.value],
        query: () =>
            apiGet(`/projects/${project.value.id}`, {
                pipeline: selectedPipelineId.value,
                page: currentPage.value,
            }),
        enabled: () =>
            Boolean(project.value && selectedPipelineId.value) &&
            (selectedPipelineId.value !== initialPipelineId.value || currentPage.value > 1),
    });

    const currentEnvelope = computed(() =>
        selectedPipelineId.value === initialPipelineId.value && currentPage.value === 1
            ? (initialEnvelope.value ?? null)
            : (pageQuery.data.value ?? null),
    );

    const shownEnvelope = ref(null);
    watch(
        currentEnvelope,
        (envelope) => {
            if (envelope) {
                shownEnvelope.value = envelope;
            }
        },
        { immediate: true },
    );

    const displayedPipeline = computed(
        () => shownEnvelope.value?.data?.pipelines?.find((pipeline) => Array.isArray(pipeline.groups)) ?? null,
    );
    const lastPage = computed(() => shownEnvelope.value?.meta?.last_page ?? 1);
    const fromItem = computed(() => shownEnvelope.value?.meta?.from ?? 0);
    const toItem = computed(() => shownEnvelope.value?.meta?.to ?? 0);
    const totalItems = computed(() => shownEnvelope.value?.meta?.total ?? 0);
    const loadingPage = computed(() => (pageQuery.isLoading.value ? currentPage.value : null));

    function fetchPage(page) {
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    }

    const breadcrumbs = computed(() => [{ name: 'Projects', route: 'projects' }]);
    const currentCrumb = computed(() => project.value?.name ?? 'Not Found');
</script>

<template>
    <Page v-if="project">
        <template #heading>Project Work</template>
        <template #description>{{ project.name }}</template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="currentCrumb" />
        </template>

        <div
            role="note"
            class="flex items-start gap-3 rounded-[var(--rounding-md)] border border-blue-200 bg-blue-50 px-4 py-3 text-blue-950"
        >
            <InformationCircleIcon class="mt-0.5 size-5 shrink-0 text-blue-600" aria-hidden="true" />
            <div class="grid gap-1">
                <p class="text-sm font-semibold">About milestone progress</p>
                <p class="text-sm text-pretty text-blue-800">
                    Milestone progress represents all work required to complete the milestone. Internal tasks may
                    contribute to progress even when they are not visible in the portal.
                </p>
            </div>
        </div>

        <PageCard>
            <PipelineSelect v-if="pipelines.length > 1" v-model="selectedPipelineId" :pipelines="pipelines" />

            <ProjectPipelineTable :groups="displayedPipeline?.groups ?? []" />

            <Pagination
                v-if="lastPage > 1"
                :current-page="currentPage"
                :last-page="lastPage"
                :from-item="fromItem"
                :to-item="toItem"
                :total-items="totalItems"
                :loading-page="loadingPage"
                @fetchPreviousPage="fetchPage(currentPage - 1)"
                @fetchNextPage="fetchPage(currentPage + 1)"
                @fetchPage="fetchPage"
            />
        </PageCard>
    </Page>

    <Page v-else>
        <template #heading>404 Not Found</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Not Found'" />
        </template>

        <PageCard>
            <EmptyState>
                <template #heading>Project Not Found</template>
                <template #description>
                    The project you are looking for does not exist or you no longer have access to it.
                </template>
                <template #actions>
                    <BaseButton tag="router-link" :to="{ name: 'projects' }" color="gray" size="md">
                        Return to Projects
                    </BaseButton>
                </template>
            </EmptyState>
        </PageCard>
    </Page>
</template>
