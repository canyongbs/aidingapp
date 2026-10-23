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
    import { ArchiveBoxIcon, ArrowLeftEndOnRectangleIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/outline';
    import { computed, defineProps } from 'vue';
    import BaseStatCard from '../ui/BaseStatCard.vue';

    const props = defineProps({
        counts: {
            type: Object,
            required: true,
        },
    });

    const cards = computed(() => [
        {
            key: 'total',
            label: 'Total',
            value: props.counts.total,
            tone: 'neutral',
            iconBg: 'bg-brand-50',
            iconColor: 'text-brand-500',
            icon: ArchiveBoxIcon,
        },
        {
            key: 'checked_out',
            label: 'Checked Out',
            value: props.counts.checked_out,
            tone: 'warning',
            iconBg: 'bg-amber-100',
            iconColor: 'text-amber-600',
            icon: ArrowLeftEndOnRectangleIcon,
        },
        {
            key: 'returned',
            label: 'Returned',
            value: props.counts.returned,
            tone: 'success',
            iconBg: 'bg-green-100',
            iconColor: 'text-green-500',
            icon: ArrowUturnLeftIcon,
        },
    ]);
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <BaseStatCard v-for="card in cards" :key="card.key" :label="card.label" :value="card.value" :tone="card.tone">
            <template #icon>
                <span :class="['flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-md', card.iconBg]">
                    <component :is="card.icon" :class="['size-5 stroke-2', card.iconColor]" aria-hidden="true" />
                </span>
            </template>
        </BaseStatCard>
    </div>
</template>
