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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
    import axios from 'axios';
    import {
        ArrowLeftIcon,
        MagnifyingGlassIcon,
        XMarkIcon,
    } from '@heroicons/vue/16/solid';
    import { ArrowRightIcon } from '@heroicons/vue/20/solid';
    import { computed, onMounted, ref, watch } from 'vue';
    import ServiceRequestCategoryTree from './ServiceRequestCategoryTree.vue';

    const props = defineProps({
        serviceRequestTypesUrl: { type: String, required: true },
    });

    const emit = defineEmits(['back']);

    // ── Step state ────────────────────────────────────────────────────────────
    // 'type-select' | 'details' | 'success'
    const step = ref('type-select');

    // ── Type select ───────────────────────────────────────────────────────────
    const activeTab = ref('new');
    const searchQuery = ref('');
    const isLoading = ref(true);
    const loadError = ref(false);
    const rawData = ref(null);
    const expandedCategories = ref(new Set());
    const selectedType = ref(null);
    const selectedPriority = ref('');

    onMounted(async () => {
        try {
            const { data } = await axios.get(props.serviceRequestTypesUrl);
            rawData.value = data;
        } catch {
            loadError.value = true;
        } finally {
            isLoading.value = false;
        }
    });

    const filteredTree = computed(() => {
        if (!rawData.value) return null;
        const q = searchQuery.value.toLowerCase().trim();
        if (!q) return rawData.value;

        const words = q.split(/\s+/).filter(Boolean);

        function textMatches(text) {
            const t = (text ?? '').toLowerCase();
            return words.every((w) => t.includes(w));
        }

        function anyTextMatches(...texts) {
            const combined = texts.join(' ').toLowerCase();
            return words.every((w) => combined.includes(w));
        }

        function filterCats(cats, ancestorNames = []) {
            return cats.flatMap((cat) => {
                const lineage = [...ancestorNames, cat.name];
                const catMatches = textMatches(cat.name);
                const children = filterCats(cat.children || [], lineage);
                // type matches if its name/description or any ancestor category name contains all words
                const types = cat.types.filter((t) =>
                    catMatches || anyTextMatches(t.name, t.description ?? '', ...lineage)
                );
                if (catMatches || types.length || children.length) {
                    return [{ ...cat, types: catMatches ? cat.types : types, children }];
                }
                return [];
            });
        }

        return {
            types: rawData.value.types.filter((t) => anyTextMatches(t.name, t.description ?? '')),
            categories: filterCats(rawData.value.categories),
        };
    });

    const hasResults = computed(() => {
        if (!filteredTree.value) return false;
        return filteredTree.value.types.length > 0 || filteredTree.value.categories.length > 0;
    });

    watch(searchQuery, (q) => {
        if (!q.trim() || !filteredTree.value) return;
        const s = new Set(expandedCategories.value);

        function expandAll(cats) {
            for (const cat of cats) {
                s.add(cat.id);
                if (cat.children?.length) expandAll(cat.children);
            }
        }

        expandAll(filteredTree.value.categories);
        expandedCategories.value = s;
    });

    function toggleCategory(id) {
        const s = new Set(expandedCategories.value);
        s.has(id) ? s.delete(id) : s.add(id);
        expandedCategories.value = s;
    }

    function selectType(type) {
        selectedType.value = type;
        selectedPriority.value = '';
    }

    function clearSearch() {
        searchQuery.value = '';
    }

    const selectedPriorityObject = computed(() =>
        selectedType.value?.priorities?.find((p) => p.id === selectedPriority.value) ?? null
    );

    function onContinue() {
        step.value = 'details';
    }

    // ── Details form ──────────────────────────────────────────────────────────
    const title = ref('');
    const description = ref('');
    const attachments = ref([]); // [{ originalFileName, path }] from FormKit upload
    const isSubmitting = ref(false);
    const submitError = ref(null);

    const canSubmit = computed(
        () => title.value.trim() && description.value.trim() && !isSubmitting.value
    );

    async function submitForm() {
        if (!canSubmit.value) return;

        isSubmitting.value = true;
        submitError.value = null;

        const storeUrl = rawData.value.store_url_base.replace('__TYPE__', selectedType.value.id);

        try {
            await axios.post(storeUrl, {
                title: title.value,
                description: description.value,
                priority_id: selectedPriority.value,
                attachments: (attachments.value ?? []).map((a) => ({
                    path: a.path,
                    original_file_name: a.originalFileName,
                })),
            });

            step.value = 'success';
        } catch (error) {
            submitError.value =
                error.response?.data?.errors?.[0] ?? 'Something went wrong. Please try again.';
        } finally {
            isSubmitting.value = false;
        }
    }
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- ── TYPE SELECT STEP ─────────────────────────────────────────────── -->
        <template v-if="step === 'type-select'">
            <!-- Tabs -->
            <div class="px-4 pt-4 pb-0 shrink-0">
                <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
                    <button
                        @click="activeTab = 'new'"
                        :class="[
                            'flex-1 text-sm font-medium py-2 rounded-lg transition-all',
                            activeTab === 'new' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700',
                        ]"
                    >
                        New Issue
                    </button>
                    <button
                        disabled
                        class="flex-1 text-sm font-medium py-2 rounded-lg text-gray-300 cursor-not-allowed"
                        title="Coming soon"
                    >
                        Existing Issue
                    </button>
                </div>
            </div>

            <template v-if="activeTab === 'new'">
                <!-- Search -->
                <div class="px-4 pt-3 pb-2 shrink-0">
                    <div class="relative">
                        <MagnifyingGlassIcon
                            class="pointer-events-none absolute left-3 inset-y-0 my-auto w-4 h-4 text-gray-400"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search request types…"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-9 pr-8 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="absolute right-2.5 inset-y-0 my-auto text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Tree -->
                <div class="flex-1 overflow-y-auto px-4 py-2">
                    <div v-if="isLoading" class="flex flex-col items-center justify-center h-full gap-3 text-gray-400">
                        <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                        </svg>
                        <span class="text-sm">Loading request types…</span>
                    </div>

                    <div
                        v-else-if="loadError"
                        class="flex flex-col items-center justify-center h-full gap-2 text-center px-4"
                    >
                        <p class="text-sm font-medium text-gray-700">Couldn't load request types</p>
                        <p class="text-xs text-gray-400">Please try closing and reopening the widget.</p>
                    </div>

                    <div
                        v-else-if="searchQuery && !hasResults"
                        class="flex flex-col items-center justify-center h-full gap-2 text-center px-4"
                    >
                        <p class="text-sm font-medium text-gray-700">No types match "{{ searchQuery }}"</p>
                        <button @click="clearSearch" class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                            Clear search
                        </button>
                    </div>

                    <ServiceRequestCategoryTree
                        v-else-if="filteredTree"
                        :categories="filteredTree.categories"
                        :types="filteredTree.types"
                        :expanded-categories="expandedCategories"
                        :selected-type-id="selectedType?.id ?? null"
                        :depth="0"
                        @toggle-category="toggleCategory"
                        @select-type="selectType"
                    />
                </div>

                <!-- Priority + Continue footer -->
                <Transition
                    enter-active-class="transition-all duration-150 ease-out"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                >
                    <div v-if="selectedType" class="shrink-0 border-t border-gray-100 bg-gray-50 px-4 pt-3 pb-4">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="text-xs font-medium text-gray-500">Selected</span>
                            <span class="text-xs text-gray-700 font-medium truncate">{{ selectedType.name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 relative">
                                <select
                                    v-model="selectedPriority"
                                    :disabled="!selectedType.priorities?.length"
                                    class="w-full h-9 appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 pr-8 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent disabled:opacity-40 disabled:cursor-not-allowed transition-all cursor-pointer"
                                >
                                    <option value="" disabled>
                                        {{
                                            selectedType.priorities?.length
                                                ? 'Select priority…'
                                                : 'No priorities available'
                                        }}
                                    </option>
                                    <option
                                        v-for="priority in selectedType.priorities"
                                        :key="priority.id"
                                        :value="priority.id"
                                    >
                                        {{ priority.name }}
                                    </option>
                                </select>
                                <svg
                                    class="pointer-events-none absolute right-2.5 inset-y-0 my-auto w-4 h-4 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <button
                                @click="onContinue"
                                :disabled="!selectedPriority"
                                :class="[
                                    'shrink-0 h-9 flex items-center gap-1.5 px-4 rounded-lg text-sm font-medium transition-all',
                                    selectedPriority
                                        ? 'bg-brand-500 hover:bg-brand-600 text-white shadow-sm'
                                        : 'bg-gray-100 text-gray-300 cursor-not-allowed',
                                ]"
                            >
                                Continue
                                <ArrowRightIcon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </Transition>
            </template>
        </template>

        <!-- ── DETAILS STEP ────────────────────────────────────────────────── -->
        <template v-else-if="step === 'details'">
            <!-- Context bar -->
            <div
                class="shrink-0 mx-4 mt-4 mb-1 flex items-center gap-2 px-3 py-2 bg-brand-50 border border-brand-100 rounded-xl"
            >
                <button
                    @click="step = 'type-select'"
                    class="shrink-0 text-brand-400 hover:text-brand-600 transition-colors"
                    aria-label="Back to type selection"
                >
                    <ArrowLeftIcon class="w-4 h-4" />
                </button>
                <div class="flex-1 flex items-center gap-2 min-w-0 text-xs">
                    <span class="text-brand-700 font-semibold truncate">{{ selectedType.name }}</span>
                    <span class="text-brand-300 shrink-0">·</span>
                    <span class="text-brand-500 truncate">{{ selectedPriorityObject?.name }}</span>
                </div>
            </div>

            <!-- Form -->
            <div class="flex-1 overflow-y-auto px-4 py-3 flex flex-col">
                <FormKit
                    type="text"
                    name="title"
                    label="Title"
                    placeholder="Brief summary of the issue"
                    validation="required"
                    :validation-messages="{ required: 'Title is required.' }"
                    v-model="title"
                    outer-class="!max-w-none"
                    inner-class="!max-w-none !rounded-xl"
                />

                <FormKit
                    type="textarea"
                    name="description"
                    label="Description"
                    placeholder="Describe the issue in detail…"
                    validation="required"
                    :validation-messages="{ required: 'Description is required.' }"
                    v-model="description"
                    outer-class="!max-w-none"
                    inner-class="!max-w-none !rounded-xl"
                    input-class="!h-28"
                />

                <FormKit
                    v-if="rawData?.upload_url"
                    type="upload"
                    name="attachments"
                    label="Attachments"
                    :upload-url="rawData.upload_url"
                    :multiple="true"
                    :accept="['*/*']"
                    :limit="10"
                    :size="25"
                    v-model="attachments"
                    outer-class="!max-w-none"
                />

                <!-- Submit error -->
                <div v-if="submitError" class="px-3 py-2.5 rounded-xl bg-red-50 border border-red-100 mt-2">
                    <p class="text-sm text-red-600">{{ submitError }}</p>
                </div>
            </div>

            <!-- Submit footer -->
            <div class="shrink-0 px-4 pb-4 pt-3 border-t border-gray-100">
                <button
                    @click="submitForm"
                    :disabled="!canSubmit"
                    :class="[
                        'w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-medium transition-all',
                        canSubmit
                            ? 'bg-brand-500 hover:bg-brand-600 text-white shadow-sm'
                            : 'bg-gray-100 text-gray-300 cursor-not-allowed',
                    ]"
                >
                    <svg
                        v-if="isSubmitting"
                        class="w-4 h-4 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                    </svg>
                    {{ isSubmitting ? 'Submitting…' : 'Submit Service Request' }}
                    <ArrowRightIcon v-if="!isSubmitting" class="w-4 h-4" />
                </button>
            </div>
        </template>

        <!-- ── SUCCESS STEP ────────────────────────────────────────────────── -->
        <template v-else-if="step === 'success'">
            <div class="flex-1 flex flex-col items-center justify-center px-8 py-10 text-center gap-5">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                    <svg
                        class="w-8 h-8 text-green-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="text-lg font-semibold text-gray-900">Request Submitted</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Your service request
                        <span class="font-medium text-gray-700">"{{ title }}"</span>
                        has been received. Our team will get back to you soon.
                    </p>
                </div>

                <button
                    @click="$emit('back')"
                    class="mt-2 flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium transition-all shadow-sm"
                >
                    <ArrowLeftIcon class="w-4 h-4" />
                    Back to Assistant Chat
                </button>
            </div>
        </template>
    </div>
</template>
