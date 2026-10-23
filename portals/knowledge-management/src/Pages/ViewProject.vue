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
    import BaseCallout from '@common/BaseCallout.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import { InboxIcon } from '@heroicons/vue/24/outline';
    import { computed, ref, watch } from 'vue';
    import LoadFailedState from '../Components/LoadFailedState.vue';
    import PipelineSelect from '../Components/Projects/PipelineSelect.vue';
    import ProjectPipelineTable from '../Components/Projects/ProjectPipelineTable.vue';
    import { usePaginatedEnvelope } from '../Composables/usePaginatedEnvelope.js';
    import { apiGet } from '../Services/api.js';
    import { useProjectData } from './loaders.js';

    const { data: initialEnvelope } = useProjectData();

    const project = computed(() => initialEnvelope.value?.data ?? null);
    const pipelines = computed(() => project.value?.pipelines ?? []);
    const initialPipelineId = computed(
        () => pipelines.value.find((pipeline) => Array.isArray(pipeline.groups))?.id ?? pipelines.value[0]?.id ?? null,
    );

    const selectedPipelineId = ref(null);

    watch(
        pipelines,
        (value) => {
            if (!value.some((pipeline) => pipeline.id === selectedPipelineId.value)) {
                selectedPipelineId.value = value[0]?.id ?? null;
            }
        },
        { immediate: true },
    );

    const { currentPage, shownEnvelope, isFetching, loadFailed, pagination, refetch } = usePaginatedEnvelope({
        initialEnvelope,
        usesInitialEnvelope: (page) => selectedPipelineId.value === initialPipelineId.value && page === 1,
        key: (page) => ['knowledge-management', 'project', project.value?.id, selectedPipelineId.value, page],
        query: (page) => apiGet(`/projects/${project.value.id}`, { pipeline: selectedPipelineId.value, page }),
        enabled: () => Boolean(project.value && selectedPipelineId.value),
    });

    watch(selectedPipelineId, (value, previousValue) => {
        if (previousValue !== null && value !== previousValue) {
            currentPage.value = 1;
        }
    });

    const groups = computed(
        () => shownEnvelope.value?.data?.pipelines?.find((pipeline) => Array.isArray(pipeline.groups))?.groups ?? [],
    );

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

        <BaseCallout
            size="sm"
            tone="info"
            title="About milestone progress"
            description="Milestone progress represents all work required to complete the milestone. Internal tasks may contribute to progress even when they are not visible in the portal."
        />

        <ProjectPipelineTable :groups="groups" :loading="isFetching">
            <template v-if="pipelines.length > 1" #toolbar>
                <PipelineSelect v-model="selectedPipelineId" :pipelines="pipelines" />
            </template>

            <template v-if="loadFailed || groups.length === 0" #empty>
                <LoadFailedState v-if="loadFailed" heading="Could not load pipeline tasks" @retry="refetch" />

                <EmptyState role="status" v-else :icon="InboxIcon" :contained="false">
                    <template #heading>No pipeline tasks</template>
                    <template #description>There are no tasks to display for this pipeline yet.</template>
                </EmptyState>
            </template>

            <template v-if="!loadFailed && pagination.totalItems > 0" #footer>
                <Pagination v-bind="pagination" />
            </template>
        </ProjectPipelineTable>
    </Page>

    <Page v-else>
        <template #heading>404 Not Found</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Not Found'" />
        </template>

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
    </Page>
</template>
