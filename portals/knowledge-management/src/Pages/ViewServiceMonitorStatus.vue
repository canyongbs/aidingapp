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
    import { ArrowLeftIcon } from '@heroicons/vue/16/solid';
    import { ChartBarIcon } from '@heroicons/vue/24/outline';
    import { useQuery } from '@pinia/colada';
    import { computed, ref, watch } from 'vue';
    import ServiceMonitorDetailsCard from '../Components/ServiceMonitors/ServiceMonitorDetailsCard.vue';
    import ServiceMonitorHealthNote from '../Components/ServiceMonitors/ServiceMonitorHealthNote.vue';
    import {
        DEFAULT_HISTORY_PERIOD,
        HISTORY_PERIOD_OPTIONS,
        getHistoryPeriod,
    } from '../Components/ServiceMonitors/serviceMonitorHistory.js';
    import ServiceMonitorHistoryChart from '../Components/ServiceMonitors/ServiceMonitorHistoryChart.vue';
    import ServiceMonitorStatusBanner from '../Components/ServiceMonitors/ServiceMonitorStatusBanner.vue';
    import ServiceMonitorStatusBannerMeta from '../Components/ServiceMonitors/ServiceMonitorStatusBannerMeta.vue';
    import { getStatus } from '../Components/ServiceMonitors/serviceMonitorStatuses.js';
    import ServiceMonitorUptimeCards from '../Components/ServiceMonitors/ServiceMonitorUptimeCards.vue';
    import BaseCard from '../Components/ui/BaseCard.vue';
    import BaseSelect from '../Components/ui/BaseSelect.vue';
    import { useNow } from '../Composables/useNow.js';
    import { apiGet } from '../Services/api.js';
    import formatDateTime, { resolveTimezone } from '../Services/FormatDateTime.js';
    import formatRelativeTime from '../Services/FormatRelativeTime.js';
    import { useServiceMonitorDetailData } from './loaders.js';

    // The default history period arrives via the route data loader; other periods are fetched on demand.
    const { data: initialEnvelope } = useServiceMonitorDetailData();

    const now = useNow();

    const period = ref(DEFAULT_HISTORY_PERIOD);
    const monitorId = computed(() => initialEnvelope.value?.data?.id ?? null);

    watch(monitorId, () => {
        period.value = DEFAULT_HISTORY_PERIOD;
    });

    const periodQuery = useQuery({
        key: () => ['knowledge-management', 'service-monitor', monitorId.value, period.value],
        query: () => apiGet(`/status/${monitorId.value}`, { period: period.value, timezone: resolveTimezone() }),
        enabled: () => Boolean(monitorId.value) && period.value !== DEFAULT_HISTORY_PERIOD,
    });

    const currentEnvelope = computed(() =>
        period.value === DEFAULT_HISTORY_PERIOD ? (initialEnvelope.value ?? null) : (periodQuery.data.value ?? null),
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

    const monitor = computed(() => (initialEnvelope.value ? (shownEnvelope.value?.data ?? null) : null));
    const statusConfig = computed(() => getStatus(monitor.value?.status));
    const historyPeriodConfig = computed(() => getHistoryPeriod(monitor.value?.history_period));
    const isFetchingHistory = computed(() => period.value !== DEFAULT_HISTORY_PERIOD && periodQuery.isLoading.value);

    const breadcrumbs = [{ name: 'Status', route: 'status' }];
</script>

<template>
    <Page v-if="monitor">
        <template #heading>{{ monitor.name }}</template>
        <template v-if="monitor.description" #description>{{ monitor.description }}</template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="monitor.name" />
        </template>

        <div>
            <BaseButton tag="router-link" :to="{ name: 'status' }" color="gray" size="sm" :icon="ArrowLeftIcon">
                Back to all monitors
            </BaseButton>
        </div>

        <ServiceMonitorStatusBanner
            :status="monitor.status"
            :title="statusConfig.label"
            :description="statusConfig.description"
        >
            <template #meta>
                <ServiceMonitorStatusBannerMeta label="Last checked">
                    <time
                        v-if="monitor.last_checked_at"
                        :datetime="monitor.last_checked_at"
                        :title="formatDateTime(monitor.last_checked_at)"
                    >
                        {{ formatRelativeTime(monitor.last_checked_at, now) }}
                    </time>
                    <template v-else>Never</template>
                </ServiceMonitorStatusBannerMeta>
                <ServiceMonitorStatusBannerMeta label="Monitoring every">
                    {{ monitor.frequency_label }}
                </ServiceMonitorStatusBannerMeta>
            </template>
        </ServiceMonitorStatusBanner>

        <ServiceMonitorUptimeCards :uptime-percentages="monitor.uptime_percentages" />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <BaseCard class="min-w-0 lg:col-span-2">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex gap-3">
                        <ChartBarIcon class="size-6 shrink-0 text-gray-700" aria-hidden="true" />
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">{{ historyPeriodConfig.heading }}</h2>
                            <p class="text-sm text-gray-500">{{ historyPeriodConfig.description }}</p>
                        </div>
                    </div>
                    <BaseSelect
                        v-model="period"
                        :options="HISTORY_PERIOD_OPTIONS"
                        label="Uptime history period"
                        class="sm:w-44"
                    />
                </div>

                <ServiceMonitorHistoryChart
                    class="mt-8"
                    :history="monitor.history"
                    :period="monitor.history_period"
                    :loading="isFetchingHistory"
                />
            </BaseCard>

            <ServiceMonitorDetailsCard :monitor="monitor" />
        </div>

        <ServiceMonitorHealthNote :status="monitor.status" />
    </Page>

    <Page v-else>
        <template #heading>404 Not Found</template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="'Not Found'" />
        </template>

        <PageCard>
            <EmptyState>
                <template #heading>Monitor Not Found</template>
                <template #description>
                    The monitor you are looking for does not exist or you no longer have access to it.
                </template>
                <template #actions>
                    <BaseButton tag="router-link" :to="{ name: 'status' }" color="gray" size="md">
                        Return to Status
                    </BaseButton>
                </template>
            </EmptyState>
        </PageCard>
    </Page>
</template>
