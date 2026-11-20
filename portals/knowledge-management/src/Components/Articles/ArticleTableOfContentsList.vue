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
    import { ChevronRightIcon } from '@heroicons/vue/20/solid';
    import { computed, ref } from 'vue';

    const props = defineProps({
        items: {
            type: Array,
            required: true,
        },
        depth: {
            type: Number,
            default: 0,
        },
    });

    const emit = defineEmits(['navigate']);

    // Every group starts expanded, so only the groups a reader collapses are tracked.
    const collapsedIds = ref(new Set());

    const hasExpandableItems = computed(() => props.items.some((item) => item.children.length > 0));

    function toggle(id) {
        if (collapsedIds.value.has(id)) {
            collapsedIds.value.delete(id);
        } else {
            collapsedIds.value.add(id);
        }
    }
</script>

<template>
    <ul :class="['flex flex-col gap-y-1', depth > 0 && 'ms-2.5 mt-1 border-s border-gray-200 ps-5']">
        <li v-for="item in items" :key="item.id">
            <div class="flex items-start gap-x-1.5">
                <button
                    v-if="item.children.length > 0"
                    type="button"
                    class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded text-gray-400 outline-none transition duration-75 hover:text-gray-600 focus-visible:ring-2 focus-visible:ring-brand-600"
                    :aria-expanded="!collapsedIds.has(item.id)"
                    :aria-label="`${collapsedIds.has(item.id) ? 'Expand' : 'Collapse'} ${item.text}`"
                    @click="toggle(item.id)"
                >
                    <ChevronRightIcon
                        :class="['size-4 transition-transform', !collapsedIds.has(item.id) && 'rotate-90']"
                        aria-hidden="true"
                    />
                </button>
                <span v-else-if="hasExpandableItems" class="size-5 shrink-0" aria-hidden="true" />

                <a
                    :href="`#${item.id}`"
                    :class="[
                        'min-w-0 break-words rounded py-0.5 text-sm outline-none transition duration-75 hover:text-brand-600 focus-visible:ring-2 focus-visible:ring-brand-600',
                        depth === 0 ? 'font-medium text-gray-900' : 'text-gray-600',
                    ]"
                    @click.prevent="emit('navigate', item.id)"
                >
                    {{ item.text }}
                </a>
            </div>

            <ArticleTableOfContentsList
                v-if="item.children.length > 0"
                v-show="!collapsedIds.has(item.id)"
                :items="item.children"
                :depth="depth + 1"
                @navigate="emit('navigate', $event)"
            />
        </li>
    </ul>
</template>
