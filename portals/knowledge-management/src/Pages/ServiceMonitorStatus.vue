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
    import { computed, ref, watch } from 'vue';
    import ServiceMonitorCard from '../Components/ServiceMonitorCard.vue';
    import { apiGet } from '../Services/api.js';
    import { useServiceMonitorData } from './loaders.js';

    // Page 1 arrives via the route data loader; subsequent pages are fetched on demand.
    const { data: initialData } = useServiceMonitorData();

    const result = ref([]);
    const loadingPage = ref(null);

    const currentPage = ref(1);
    const lastPage = ref(1);
    const totalArticles = ref(0);
    const fromArticle = ref(0);
    const toArticle = ref(0);

    const okTitle = 'All systems operational';
    const okMessage =
        'All systems are functioning seamlessly, with no disruptions or downtime reported. Every component, from critical infrastructure to auxiliary services is running at full capacity, ensuring optimal performance and reliability.';
    const issueTitle = 'Some systems are experiencing issues';
    const issueMessage = 'One or more services are currently experiencing disruptions or downtime.';

    const hasIssues = computed(() =>
        result.value.some((serviceMonitor) => serviceMonitor.latest_history?.succeeded === false),
    );

    const hasAnyHistory = computed(() => result.value.some((serviceMonitor) => serviceMonitor.latest_history !== null));

    const systemTitle = computed(() => (hasIssues.value ? issueTitle : okTitle));
    const systemMessage = computed(() => (hasIssues.value ? issueMessage : okMessage));

    function setPagination(pagination) {
        currentPage.value = pagination.current_page;
        lastPage.value = pagination.last_page;
        totalArticles.value = pagination.total;
        fromArticle.value = pagination.from;
        toArticle.value = pagination.to;
    }

    function applyResponse(response) {
        if (!response) {
            return;
        }

        result.value = response.data;
        setPagination(response.meta);
    }

    watch(initialData, applyResponse, { immediate: true });

    async function getServiceMonitors(page = 1) {
        loadingPage.value = page;

        try {
            applyResponse(await apiGet('/status', { page }));
        } catch (error) {
            console.error('Error fetching service monitors:', error);
        } finally {
            loadingPage.value = null;
        }
    }

    function fetchNextPage() {
        if (currentPage.value < lastPage.value) {
            getServiceMonitors(currentPage.value + 1);
        }
    }

    function fetchPreviousPage() {
        if (currentPage.value > 1) {
            getServiceMonitors(currentPage.value - 1);
        }
    }

    function fetchPage(page) {
        getServiceMonitors(page);
    }
</script>

<template>
    <Page>
        <template #heading> Status </template>
        <template #description> Real-time status of services and systems </template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Status'" />
        </template>

        <template v-if="result.length > 0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <ServiceMonitorCard
                    v-for="(serviceMonitor, index) in result"
                    :key="index"
                    :name="serviceMonitor.name"
                    :status="serviceMonitor.latest_history?.succeeded ?? true"
                    :message="
                        serviceMonitor.latest_history?.status_message ?? 'No known issues (monitoring not yet started).'
                    "
                />
            </div>

            <Pagination
                v-if="lastPage > 1"
                :currentPage="currentPage"
                :lastPage="lastPage"
                :fromItem="fromArticle"
                :toItem="toArticle"
                :totalItems="totalArticles"
                :loadingPage="loadingPage"
                @fetchNextPage="fetchNextPage"
                @fetchPreviousPage="fetchPreviousPage"
                @fetchPage="fetchPage"
            />
        </template>

        <EmptyState v-else>
            <template #heading>There are no service monitors to display.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md"> Return Home </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
