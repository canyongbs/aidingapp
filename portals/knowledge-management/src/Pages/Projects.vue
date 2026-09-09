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
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import Page from '@common/portal/Page.vue';
    import PageCard from '@common/portal/PageCard.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import { useQuery } from '@pinia/colada';
    import { computed, ref, watch } from 'vue';
    import ProjectsTable from '../Components/Projects/ProjectsTable.vue';
    import { apiGet } from '../Services/api.js';
    import { useProjectsData } from './loaders.js';

    const { data: initialData } = useProjectsData();
    const currentPage = ref(1);

    const pageQuery = useQuery({
        key: () => ['knowledge-management', 'projects', currentPage.value],
        query: () => apiGet('/projects', { page: currentPage.value }),
        enabled: () => currentPage.value > 1,
    });

    const currentEnvelope = computed(() =>
        currentPage.value > 1 ? (pageQuery.data.value ?? null) : (initialData.value ?? null),
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

    const projects = computed(() => shownEnvelope.value?.data ?? []);
    const lastPage = computed(() => shownEnvelope.value?.meta?.last_page ?? 1);
    const fromItem = computed(() => shownEnvelope.value?.meta?.from ?? 0);
    const toItem = computed(() => shownEnvelope.value?.meta?.to ?? 0);
    const totalItems = computed(() => shownEnvelope.value?.meta?.total ?? 0);
    const loadingPage = computed(() => (currentPage.value > 1 && pageQuery.isLoading.value ? currentPage.value : null));

    function fetchPage(page) {
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    }
</script>

<template>
    <Page>
        <template #heading>Projects</template>
        <template #description>Select a project to view its work pipeline</template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Projects'" />
        </template>

        <PageCard>
            <ProjectsTable :projects="projects" />

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
</template>
