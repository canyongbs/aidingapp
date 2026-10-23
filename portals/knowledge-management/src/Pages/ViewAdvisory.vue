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
    import BaseBadge from '@common/BaseBadge.vue';
    import BaseButton from '@common/BaseButton.vue';
    import BaseSection from '@common/BaseSection.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import { ArrowLeftIcon } from '@heroicons/vue/16/solid';
    import { computed } from 'vue';
    import { getStatusColor } from '../Components/Advisories/advisoryPresentation.js';
    import AdvisoryUpdatesTimeline from '../Components/Advisories/AdvisoryUpdatesTimeline.vue';
    import { useAdvisoryData } from './loaders.js';

    const { data: envelope } = useAdvisoryData();

    const advisory = computed(() => envelope.value?.data ?? null);

    const breadcrumbs = [{ name: 'Advisories', route: 'advisories' }];
</script>

<template>
    <Page v-if="advisory">
        <template #heading>{{ advisory.title }}</template>
        <template v-if="advisory.department_name" #description>
            Assigned department: {{ advisory.department_name }}
        </template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="advisory.title" />
        </template>

        <template #belowHeaderContent>
            <dl class="flex flex-wrap gap-x-8 gap-y-3">
                <div v-if="advisory.severity" class="flex flex-col gap-1.5">
                    <dt class="text-xs font-medium text-white/75">Severity</dt>
                    <dd>
                        <BaseBadge :color="advisory.severity.color" size="md">
                            {{ advisory.severity.name }}
                        </BaseBadge>
                    </dd>
                </div>
                <div v-if="advisory.status" class="flex flex-col gap-1.5">
                    <dt class="text-xs font-medium text-white/75">Status</dt>
                    <dd>
                        <BaseBadge :color="getStatusColor(advisory.status)" size="md">
                            {{ advisory.status.name }}
                        </BaseBadge>
                    </dd>
                </div>
            </dl>
        </template>

        <div>
            <BaseButton tag="router-link" :to="{ name: 'advisories' }" color="gray" size="sm" :icon="ArrowLeftIcon">
                Back to advisories
            </BaseButton>
        </div>

        <BaseSection heading="Description">
            <p class="whitespace-pre-line break-words text-sm text-gray-700">{{ advisory.description }}</p>
        </BaseSection>

        <BaseSection heading="Updates">
            <AdvisoryUpdatesTimeline v-if="advisory.updates.length > 0" :updates="advisory.updates" />
            <p v-else class="text-sm text-gray-500">There are no updates for this advisory yet.</p>
        </BaseSection>
    </Page>

    <Page v-else>
        <template #heading>404 Not Found</template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="'Not Found'" />
        </template>

        <EmptyState>
            <template #heading>Advisory Not Found</template>
            <template #description>
                The advisory you are looking for does not exist or you no longer have access to it.
            </template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'advisories' }" color="gray" size="md">
                    Return to Advisories
                </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
