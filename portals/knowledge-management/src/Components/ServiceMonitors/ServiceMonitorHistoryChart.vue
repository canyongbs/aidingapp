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
    import { computed, ref } from 'vue';
    import { describeBucket, formatBucketStart, getHistoryPeriod } from './serviceMonitorHistory.js';
    import { STATUS_ORDER, getStatus } from './serviceMonitorStatuses.js';

    const props = defineProps({
        history: {
            type: Array,
            required: true,
        },
        period: {
            type: String,
            required: true,
        },
        loading: {
            type: Boolean,
            default: false,
        },
    });

    /**
     * The smallest bar height, as a percentage, so that buckets without any successful checks remain visible.
     */
    const MINIMUM_BAR_HEIGHT = 4;

    const periodConfig = computed(() => getHistoryPeriod(props.period));

    const bars = computed(() =>
        props.history.map((bucket, index) => ({
            key: bucket.starts_at,
            index,
            height: Math.max(bucket.uptime_percentage ?? 0, MINIMUM_BAR_HEIGHT),
            barClass: getStatus(bucket.status).barClass,
            description: describeBucket(bucket, props.period),
            axisLabel:
                index % periodConfig.value.axisLabelEvery === 0
                    ? formatBucketStart(bucket.starts_at, periodConfig.value.axisFormat)
                    : null,
        })),
    );

    const legend = STATUS_ORDER.map((status) => ({ status, ...getStatus(status) }));

    const activeIndex = ref(null);
    const activeBar = computed(() => (activeIndex.value === null ? null : (bars.value[activeIndex.value] ?? null)));

    const tooltipStyle = computed(() => {
        const position = ((activeBar.value.index + 0.5) / bars.value.length) * 100;

        // Keep the tooltip within the chart when hovering the bars at either end.
        const translate = position < 20 ? '0%' : position > 80 ? '-100%' : '-50%';

        return { left: `${position}%`, transform: `translate(${translate}, -100%)` };
    });
</script>

<template>
    <div>
        <div class="flex gap-3">
            <div class="relative h-40 w-9 shrink-0 text-right text-xs tabular-nums text-gray-500" aria-hidden="true">
                <span
                    v-for="tick in [100, 50, 0]"
                    :key="tick"
                    class="absolute right-0 -translate-y-1/2"
                    :style="{ top: `${100 - tick}%` }"
                >
                    {{ tick }}%
                </span>
            </div>

            <div class="relative min-w-0 flex-1">
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 flex h-40 flex-col justify-between"
                    aria-hidden="true"
                >
                    <span class="border-t border-dashed border-gray-200" />
                    <span class="border-t border-dashed border-gray-200" />
                    <span class="border-t border-gray-200" />
                </div>

                <ul
                    :class="[
                        'relative flex h-40 items-end gap-0.5 transition-opacity sm:gap-1',
                        loading && 'opacity-50',
                    ]"
                    :aria-label="periodConfig.heading"
                    :aria-busy="loading"
                >
                    <li
                        v-for="bar in bars"
                        :key="bar.key"
                        class="flex h-full min-w-0 flex-1 items-end rounded-t-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[rgba(var(--primary-500),1)]"
                        tabindex="0"
                        :aria-label="bar.description"
                        @mouseenter="activeIndex = bar.index"
                        @mouseleave="activeIndex = null"
                        @focus="activeIndex = bar.index"
                        @blur="activeIndex = null"
                    >
                        <span
                            :class="[
                                'block w-full rounded-t-sm transition-[height,opacity] duration-300',
                                bar.barClass,
                                activeBar && activeBar.index !== bar.index && 'opacity-60',
                            ]"
                            :style="{ height: `${bar.height}%` }"
                        />
                    </li>
                </ul>

                <div
                    v-if="activeBar"
                    role="tooltip"
                    class="pointer-events-none absolute -top-2 z-10 whitespace-nowrap rounded-[var(--rounding-md)] bg-gray-900 px-2.5 py-1.5 text-xs text-white shadow-lg"
                    :style="tooltipStyle"
                >
                    {{ activeBar.description }}
                </div>

                <div class="mt-2 flex h-4 gap-0.5 text-xs text-gray-500 sm:gap-1" aria-hidden="true">
                    <span v-for="bar in bars" :key="bar.key" class="relative min-w-0 flex-1">
                        <span
                            v-if="bar.axisLabel"
                            :class="[
                                'absolute whitespace-nowrap',
                                bar.index === 0 ? 'left-0' : 'left-1/2 -translate-x-1/2',
                            ]"
                        >
                            {{ bar.axisLabel }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <ul class="mt-5 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-gray-700" aria-label="Legend">
            <li v-for="item in legend" :key="item.status" class="inline-flex items-center gap-2">
                <span :class="['size-2.5 rounded-full', item.barClass]" aria-hidden="true" />
                {{ item.label }}
            </li>
        </ul>
    </div>
</template>
