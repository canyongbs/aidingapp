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
    import { ChevronDownIcon, ListBulletIcon } from '@heroicons/vue/20/solid';
    import { computed, nextTick, ref, useId } from 'vue';
    import ArticleTableOfContentsList from './ArticleTableOfContentsList.vue';

    const props = defineProps({
        headings: {
            type: Array,
            required: true,
        },
        collapsible: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['navigate']);

    const titleId = useId();
    const panelId = useId();
    const isOpen = ref(false);

    // Nest each heading under the closest heading before it that has a higher level.
    const items = computed(() => {
        const rootItems = [];
        const ancestors = [];

        for (const heading of props.headings) {
            const item = { ...heading, children: [] };

            while (ancestors.length > 0 && ancestors[ancestors.length - 1].level >= heading.level) {
                ancestors.pop();
            }

            (ancestors.length > 0 ? ancestors[ancestors.length - 1].children : rootItems).push(item);
            ancestors.push(item);
        }

        return rootItems;
    });

    async function navigate(id) {
        if (props.collapsible) {
            // Close the panel first, so the article has moved into place before jumping to the heading.
            isOpen.value = false;

            await nextTick();
        }

        emit('navigate', id);
    }
</script>

<template>
    <nav :aria-labelledby="titleId">
        <template v-if="collapsible">
            <button
                type="button"
                class="flex w-full items-center gap-x-3 px-6 py-4 text-start outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-600"
                :aria-expanded="isOpen"
                :aria-controls="panelId"
                @click="isOpen = !isOpen"
            >
                <ListBulletIcon class="size-5 shrink-0 text-gray-500" aria-hidden="true" />
                <span :id="titleId" class="flex-1 text-sm font-semibold text-gray-900">On this page</span>
                <ChevronDownIcon
                    :class="['size-5 shrink-0 text-gray-500 transition-transform', isOpen && 'rotate-180']"
                    aria-hidden="true"
                />
            </button>

            <div v-show="isOpen" :id="panelId" class="max-h-80 overflow-y-auto border-t border-gray-200 px-6 py-4">
                <ArticleTableOfContentsList :items="items" @navigate="navigate" />
            </div>
        </template>

        <template v-else>
            <p :id="titleId" class="mb-4 text-sm font-semibold text-gray-900">On this page</p>

            <ArticleTableOfContentsList :items="items" @navigate="navigate" />
        </template>
    </nav>
</template>
