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
    import Pagination from '@common/portal/Pagination.vue';
    import { ExclamationTriangleIcon, FolderIcon } from '@heroicons/vue/24/outline';
    import { computed } from 'vue';
    import ProjectsTable from '../Components/Projects/ProjectsTable.vue';
    import { usePaginatedEnvelope } from '../Composables/usePaginatedEnvelope.js';
    import { apiGet } from '../Services/api.js';
    import { useProjectsData } from './loaders.js';

    const { data: initialData } = useProjectsData();

    const { shownEnvelope, isFetching, loadFailed, pagination, refetch } = usePaginatedEnvelope({
        initialEnvelope: initialData,
        usesInitialEnvelope: (page) => page === 1,
        key: (page) => ['knowledge-management', 'projects', page],
        query: (page) => apiGet('/projects', { page }),
    });

    const projects = computed(() => shownEnvelope.value?.data ?? []);
</script>

<template>
    <Page>
        <template #heading>Projects</template>
        <template #description>Select a project to view its work pipeline</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Projects'" />
        </template>

        <ProjectsTable :projects="projects" :loading="isFetching">
            <template v-if="loadFailed || projects.length === 0" #empty>
                <EmptyState role="status" v-if="loadFailed" :icon="ExclamationTriangleIcon" :contained="false">
                    <template #heading>Could not load projects</template>
                    <template #description>Something went wrong while loading these results.</template>
                    <template #actions>
                        <BaseButton color="gray" size="md" @click="refetch">Try again</BaseButton>
                    </template>
                </EmptyState>

                <EmptyState role="status" v-else :icon="FolderIcon" :contained="false">
                    <template #heading>No projects to display</template>
                    <template #description>You don't currently have visibility into any projects.</template>
                </EmptyState>
            </template>

            <template v-if="!loadFailed && projects.length > 0" #footer>
                <Pagination v-bind="pagination" />
            </template>
        </ProjectsTable>
    </Page>
</template>
