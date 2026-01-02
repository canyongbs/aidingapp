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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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
    import { computed, ref, watch } from 'vue';
    import Avatar from './ui/Avatar.vue';

    const props = defineProps({
        items: { type: Array, required: true },
        command: { type: Function, required: true },
    });

    const selectedIndex = ref(0);

    const hasItems = computed(() => props.items.length > 0);

    watch(
        () => props.items,
        () => {
            selectedIndex.value = 0;
        },
    );

    function onKeyDown({ event }) {
        if (event.key === 'ArrowUp') {
            upHandler();
            return true;
        }

        if (event.key === 'ArrowDown') {
            downHandler();
            return true;
        }

        if (event.key === 'Enter') {
            enterHandler();
            return true;
        }

        return false;
    }

    function upHandler() {
        selectedIndex.value = (selectedIndex.value + props.items.length - 1) % props.items.length;
    }

    function downHandler() {
        selectedIndex.value = (selectedIndex.value + 1) % props.items.length;
    }

    function enterHandler() {
        selectItem(selectedIndex.value);
    }

    function selectItem(index) {
        const item = props.items[index];

        if (item) {
            props.command({ id: item.id, label: item.name });
        }
    }

    defineExpose({
        onKeyDown,
    });
</script>

<template>
    <div
        v-if="hasItems"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black/5 dark:ring-white/10 overflow-hidden max-h-64 overflow-y-auto"
    >
        <button
            v-for="(item, index) in items"
            :key="item.id"
            type="button"
            class="w-full flex items-center gap-3 px-3 py-2 text-left transition-colors"
            :class="[
                index === selectedIndex
                    ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            ]"
            @click="selectItem(index)"
        >
            <Avatar :src="item.avatar_url" :name="item.name" size="sm" />
            <span class="text-sm font-medium truncate">{{ item.name }}</span>
        </button>
    </div>
    <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black/5 dark:ring-white/10 px-3 py-2">
        <span class="text-sm text-gray-500 dark:text-gray-400">No results</span>
    </div>
</template>
