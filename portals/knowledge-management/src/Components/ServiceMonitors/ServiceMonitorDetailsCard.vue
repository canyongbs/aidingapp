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
    import { ArrowTopRightOnSquareIcon } from '@heroicons/vue/16/solid';
    import { ClockIcon, Cog6ToothIcon, LinkIcon, Square3Stack3DIcon } from '@heroicons/vue/24/outline';
    import { computed } from 'vue';
    import BaseCard from '../ui/BaseCard.vue';

    const props = defineProps({
        monitor: {
            type: Object,
            required: true,
        },
    });

    // Only link to web addresses, never to other schemes such as `javascript:`.
    const isLinkable = computed(() => /^https?:\/\//i.test(props.monitor.domain ?? ''));
</script>

<template>
    <BaseCard class="h-full">
        <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900">
            <Cog6ToothIcon class="size-5 text-gray-500" aria-hidden="true" />
            Monitoring details
        </h2>

        <dl class="mt-5 grid gap-5">
            <div class="flex gap-3">
                <Square3Stack3DIcon class="size-5 shrink-0 text-gray-400" aria-hidden="true" />
                <div class="grid min-w-0 flex-1 gap-1 sm:grid-cols-[9rem_1fr] sm:gap-3">
                    <dt class="text-sm text-gray-500">Monitor type</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ monitor.monitor_type_label }}</dd>
                </div>
            </div>

            <div class="flex gap-3">
                <LinkIcon class="size-5 shrink-0 text-gray-400" aria-hidden="true" />
                <div class="grid min-w-0 flex-1 gap-1 sm:grid-cols-[9rem_1fr] sm:gap-3">
                    <dt class="text-sm text-gray-500">Endpoint / URL</dt>
                    <dd class="min-w-0 text-sm font-medium">
                        <a
                            v-if="isLinkable"
                            :href="monitor.domain"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-start gap-1 break-all text-[rgba(var(--primary-600),1)] hover:underline"
                        >
                            {{ monitor.domain }}
                            <ArrowTopRightOnSquareIcon class="mt-0.5 size-4 shrink-0" aria-hidden="true" />
                            <span class="sr-only">(opens in a new tab)</span>
                        </a>
                        <span v-else class="break-all text-gray-900">{{ monitor.domain }}</span>
                    </dd>
                </div>
            </div>

            <div class="flex gap-3">
                <ClockIcon class="size-5 shrink-0 text-gray-400" aria-hidden="true" />
                <div class="grid min-w-0 flex-1 gap-1 sm:grid-cols-[9rem_1fr] sm:gap-3">
                    <dt class="text-sm text-gray-500">Monitoring cadence</dt>
                    <dd class="text-sm font-medium text-gray-900">Every {{ monitor.frequency_label }}</dd>
                </div>
            </div>
        </dl>
    </BaseCard>
</template>
