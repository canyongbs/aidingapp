<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    import {
        Combobox,
        ComboboxButton,
        ComboboxInput,
        ComboboxOption,
        ComboboxOptions,
        TransitionRoot,
    } from '@headlessui/vue';
    import { ArrowPathIcon, CheckIcon, ChevronUpDownIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { useDebounceFn } from '@vueuse/core';
    import { computed, ref, watch } from 'vue';
    import api from '../services/api';
    import Avatar from './ui/Avatar.vue';

    const props = defineProps({
        selectedIds: { type: Array, default: () => [] },
        excludeIds: { type: Array, default: () => [] },
        maxSelections: { type: Number, default: undefined },
    });

    const emit = defineEmits(['update:selectedIds']);

    const searchQuery = ref('');
    const searchResults = ref([]);
    const selectedUsers = ref([]);
    const isSearching = ref(false);

    const canSelectMore = computed(() => {
        if (!props.maxSelections) return true;
        return selectedUsers.value.length < props.maxSelections;
    });

    const filteredResults = computed(() => {
        return searchResults.value.filter(
            (user) => !props.excludeIds.includes(user.id) && !props.selectedIds.includes(user.id),
        );
    });

    const debouncedSearch = useDebounceFn(async (query) => {
        if (!query.trim()) {
            searchResults.value = [];
            return;
        }

        isSearching.value = true;
        try {
            const response = await api.get('/users/search', {
                params: { q: query },
            });
            searchResults.value = response.data.data || [];
        } catch {
            searchResults.value = [];
        } finally {
            isSearching.value = false;
        }
    }, 300);

    watch(searchQuery, (query) => {
        debouncedSearch(query);
    });

    function handleSelection(user) {
        if (!user || !canSelectMore.value) return;

        const alreadySelected = selectedUsers.value.some((selectedUser) => selectedUser.id === user.id);
        if (alreadySelected) return;

        selectedUsers.value = [...selectedUsers.value, user];
        emit('update:selectedIds', [...props.selectedIds, user.id]);
        searchQuery.value = '';
        searchResults.value = [];
    }

    function removeUser(userId) {
        selectedUsers.value = selectedUsers.value.filter((user) => user.id !== userId);
        emit(
            'update:selectedIds',
            props.selectedIds.filter((id) => id !== userId),
        );
    }
</script>

<template>
    <div class="relative">
        <!-- Selected Users -->
        <div v-if="selectedUsers.length > 0" class="mb-2 flex flex-wrap gap-2">
            <span
                v-for="user in selectedUsers"
                :key="user.id"
                class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-3 py-1 text-sm font-medium text-primary-700 dark:bg-primary-900/30 dark:text-primary-300"
            >
                {{ user.name }}
                <button
                    type="button"
                    class="ml-1 rounded-full p-0.5 hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors"
                    @click="removeUser(user.id)"
                >
                    <XMarkIcon class="h-3 w-3" />
                </button>
            </span>
        </div>

        <!-- Search Input (hidden when max selections reached) -->
        <Combobox v-if="canSelectMore" :model-value="null" @update:model-value="handleSelection">
            <div class="relative">
                <div class="relative w-full">
                    <ComboboxInput
                        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                        placeholder="Search users..."
                        :display-value="() => searchQuery"
                        @change="searchQuery = $event.target.value"
                    />
                    <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <ArrowPathIcon v-if="isSearching" class="h-5 w-5 animate-spin text-gray-400" />
                        <ChevronUpDownIcon v-else class="h-5 w-5 text-gray-400" />
                    </ComboboxButton>
                </div>

                <TransitionRoot
                    leave="transition ease-in duration-100"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                    @after-leave="searchQuery = ''"
                >
                    <ComboboxOptions
                        v-if="searchQuery.length > 0"
                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg ring-1 ring-gray-950/5 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:ring-white/10"
                    >
                        <div
                            v-if="isSearching"
                            class="px-4 py-2.5 text-center text-sm text-gray-500 dark:text-gray-400"
                        >
                            Searching...
                        </div>

                        <div
                            v-else-if="filteredResults.length === 0"
                            class="px-4 py-2.5 text-center text-sm text-gray-500 dark:text-gray-400"
                        >
                            No users found
                        </div>

                        <ComboboxOption
                            v-for="user in filteredResults"
                            :key="user.id"
                            v-slot="{ active, selected }"
                            :value="user"
                            as="template"
                        >
                            <li
                                class="relative flex cursor-pointer select-none items-center gap-3 px-4 py-2.5 transition-colors"
                                :class="[active ? 'bg-primary-50 dark:bg-primary-900/20' : '']"
                            >
                                <Avatar :src="user.avatar_url" :name="user.name" size="sm" ring />
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium truncate"
                                        :class="[
                                            active
                                                ? 'text-primary-900 dark:text-white'
                                                : 'text-gray-900 dark:text-white',
                                        ]"
                                    >
                                        {{ user.name }}
                                    </p>
                                    <p class="text-xs truncate text-gray-500 dark:text-gray-400">
                                        {{ user.email }}
                                    </p>
                                </div>
                                <CheckIcon
                                    v-if="selected"
                                    class="h-5 w-5 flex-shrink-0 text-primary-600 dark:text-primary-400"
                                />
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </TransitionRoot>
            </div>
        </Combobox>
    </div>
</template>
