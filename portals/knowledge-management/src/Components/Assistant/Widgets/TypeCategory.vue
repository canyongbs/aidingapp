<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->
<script setup>
    import { computed, ref } from 'vue';

    const props = defineProps({
        category: {
            type: Object,
            required: true,
        },
        depth: {
            type: Number,
            default: 0,
        },
    });

    const emit = defineEmits(['select']);

    const isExpanded = ref(false);

    const hasContent = computed(() => {
        return props.category.types?.length > 0 || props.category.children?.length > 0;
    });

    const toggle = () => {
        if (hasContent.value) {
            isExpanded.value = !isExpanded.value;
        }
    };
</script>

<template>
    <div class="text-sm">
        <!-- Category Header -->
        <button
            v-if="hasContent"
            @click="toggle"
            class="w-full flex items-center gap-1 px-2 py-1 text-left text-gray-700 hover:bg-gray-100 rounded"
            :style="{ paddingLeft: `${depth * 12 + 8}px` }"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="w-4 h-4 flex-shrink-0 text-gray-400 transition-transform"
                :class="{ 'rotate-90': isExpanded }"
            >
                <path
                    fill-rule="evenodd"
                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                    clip-rule="evenodd"
                />
            </svg>
            <span class="font-medium">{{ category.name }}</span>
        </button>

        <!-- Expanded Content -->
        <div v-if="isExpanded" class="mt-1">
            <!-- Types in this category -->
            <button
                v-for="type in category.types"
                :key="type.type_id"
                @click="emit('select', type)"
                class="w-full text-left px-2 py-1 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded"
                :style="{ paddingLeft: `${(depth + 1) * 12 + 8}px` }"
            >
                {{ type.name }}
            </button>

            <!-- Child categories (recursive) -->
            <TypeCategory
                v-for="child in category.children"
                :key="child.category_id"
                :category="child"
                :depth="depth + 1"
                @select="emit('select', $event)"
            />
        </div>
    </div>
</template>
