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
    import { defineProps } from 'vue';
    import BaseButton from '../../../../resources/js/components/BaseButton.vue';
    import ResourceList from '../Components/ResourceList.vue';
    import ResourceListItem from '../Components/ResourceListItem.vue';
    import Breadcrumbs from './../Components/Breadcrumbs.vue';
    import EmptyState from './../Components/EmptyState.vue';
    import Page from './../Components/Page.vue';

    defineProps({
        categories: {
            type: Object,
            required: true,
        },
        serviceRequests: {
            type: Object,
            required: true,
        },
    });
</script>

<template>
    <Page>
        <template #heading> Service Requests </template>
        <template #description> Track and manage your support requests </template>

        <template #breadcrumbs>
            <Breadcrumbs :currentCrumb="'Service'" />
        </template>

        <ResourceList v-if="serviceRequests?.length > 0" heading="Service Requests">
            <ResourceListItem
                v-for="serviceRequest in serviceRequests"
                :key="serviceRequest.id"
                :to="{ name: 'view-service-request', params: { serviceRequestId: serviceRequest.id } }"
            >
                <template #primary>{{ serviceRequest.title }}</template>
                <template #secondary>{{ serviceRequest.number }}</template>
                <template #description>Last Updated: {{ serviceRequest.updated_at }}</template>
                <template #badge>
                    <span
                        class="inline-flex items-center rounded px-2 py-0.5 text-xs font-bold text-white"
                        :style="'background-color: rgb(' + serviceRequest.status_color + ')'"
                    >
                        {{ serviceRequest.status_name }}
                    </span>
                </template>
            </ResourceListItem>
        </ResourceList>

        <EmptyState v-else>
            <template #heading>There are no service requests to display.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'create-service-request' }" color="gray" size="md">
                    New Request
                </BaseButton>
            </template>
        </EmptyState>
    </Page>
</template>
