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
    import { computed, defineProps, onMounted, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import AppLoading from '../Components/AppLoading.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Page from '../Components/Page.vue';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';

    const route = useRoute();
    const router = useRouter();

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    const loadingResults = ref(true);
    const categories = ref([]);
    const types = ref([]);
    const currentCategory = ref(null);
    const user = ref(null);

    // Helper function to find a category by ID in the tree
    const findById = (nodes, id) => {
        for (const node of nodes) {
            if (node.id === id) return node;
            if (node.children) {
                const found = findById(node.children, id);
                if (found) return found;
            }
        }
        return null;
    };

    // Watch for route changes to update current category
    watch(
        () => route.params.categoryId,
        (categoryId) => {
            if (categoryId && categories.value.length > 0) {
                const category = findById(categories.value, categoryId);
                currentCategory.value = category || null;
            } else {
                currentCategory.value = null;
            }
        },
    );

    onMounted(function () {
        getData();
    });

    async function getData() {
        loadingResults.value = true;

        const { getUser } = useAuthStore();

        await getUser().then((authUser) => {
            user.value = authUser;
        });

        const { get } = consumer();

        get(props.apiUrl + '/service-request-type/select').then((response) => {
            categories.value = response.data.categories || [];
            types.value = response.data.types || [];

            // Set current category based on URL parameter
            const categoryId = route.params.categoryId;
            if (categoryId) {
                const category = findById(categories.value, categoryId);
                currentCategory.value = category || null;
            } else {
                currentCategory.value = null;
            }

            loadingResults.value = false;
        });
    }

    function openCategory(category) {
        // Navigate to the category using the router
        router.push({
            name: 'create-service-request',
            params: { categoryId: category.id },
        });
    }

    function backToParent() {
        if (!currentCategory.value) return;

        // find parent from breadcrumb (we store parent_id on categories)
        const parentId = currentCategory.value.parent_id;
        if (!parentId) {
            // Navigate back to root (no category)
            router.push({ name: 'create-service-request' });
            return;
        }

        // Navigate to parent category
        router.push({
            name: 'create-service-request',
            params: { categoryId: parentId },
        });
    }

    const displayedCategories = computed(() => {
        if (!currentCategory.value) return categories.value;
        return currentCategory.value.children || [];
    });

    const displayedTypes = computed(() => {
        if (!currentCategory.value) return types.value;
        return currentCategory.value.types || [];
    });

    const breadcrumbs = computed(() => {
        // Always start with Help Center
        const baseBreadcrumbs = [{ name: 'Help Center', route: 'home' }];

        // If we're at root (no category), "New Request" will be the currentCrumb
        // So we don't include it in breadcrumbs
        if (!currentCategory.value) {
            return baseBreadcrumbs;
        }

        // When in a category, add "New Request" as a breadcrumb
        const breadcrumbsWithNewRequest = [
            ...baseBreadcrumbs,
            { name: 'New Request', route: 'create-service-request' },
        ];

        // Build trail from root to current category (inclusive)
        const findTrail = (nodes, targetId, accumulator = []) => {
            for (const node of nodes) {
                const nextAccumulator = [
                    ...accumulator,
                    {
                        name: node.name,
                        id: node.id,
                        route: 'create-service-request',
                        params: { categoryId: node.id },
                    },
                ];
                if (node.id === targetId) return nextAccumulator;
                if (node.children) {
                    const found = findTrail(node.children, targetId, nextAccumulator);
                    if (found) return found;
                }
            }
            return null;
        };

        const trail = findTrail(categories.value, currentCategory.value.id, []) || [];

        // Remove the last item (current category) from the trail since it will be currentCrumb
        const parentTrail = trail.slice(0, -1);

        return [...breadcrumbsWithNewRequest, ...parentTrail];
    });

    const currentCrumb = computed(() => {
        return currentCategory.value ? currentCategory.value.name : 'New Request';
    });
</script>

<template>
    <div>
        <div v-if="loadingResults">
            <AppLoading />
        </div>
        <div v-else>
            <Page :has-new-request-button="false">
                <template #heading> Help Center </template>

                <template #description>
                    <p>Welcome {{ user.first_name }}!</p>
                    <p>We understand that you need some help, we're on it! Please complete the form below.</p>
                </template>

                <template #breadcrumbs>
                    <Breadcrumbs :currentCrumb="currentCrumb" :breadcrumbs="breadcrumbs" />
                </template>

                <main>
                    <h3 class="text-xl">
                        <span v-if="!currentCategory">Select Category</span>
                        <span v-else>{{ currentCategory.name }}</span>
                    </h3>

                    <div class="my-4 grid gap-y-4">
                        <div v-if="currentCategory" class="mb-4">
                            <button @click="backToParent" class="text-sm text-gray-500">&larr; Back</button>
                        </div>

                        <div
                            v-for="category in displayedCategories"
                            :key="category.id"
                            class="group relative bg-brand-700 text-white p-6 rounded shadow cursor-pointer hover:shadow-md"
                            @click="openCategory(category)"
                        >
                            <div class="flex items-center gap-x-3">
                                <div class="flex-shrink-0">
                                    <!-- Folder icon for categories (neutral color) -->
                                    <div
                                        class="w-10 h-10 flex items-center justify-center rounded bg-white/10 text-white/70"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-6 h-6"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M3 7.5a2.25 2.25 0 0 1 2.25-2.25h3.375a1.125 1.125 0 0 0 .9-.45L11.25 2.25h7.5A2.25 2.25 0 0 1 21 4.5v13.5A2.25 2.25 0 0 1 18.75 20.25H5.25A2.25 2.25 0 0 1 3 18V7.5z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <h3 class="text-base font-semibold leading-6">
                                        <span class="absolute inset-0" aria-hidden="true" />
                                        {{ category.name }}
                                    </h3>
                                </div>
                                <span
                                    class="pointer-events-none text-white/60 group-hover:text-white/80"
                                    aria-hidden="true"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-6 h-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8.25 4.5 15.75 12 8.25 19.5"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div
                            v-for="type in displayedTypes"
                            :key="type.id"
                            class="group relative bg-white p-6 rounded shadow border-l-4 border-transparent hover:border-brand-500"
                        >
                            <div class="flex items-center gap-x-3">
                                <div class="flex-shrink-0">
                                    <!-- Type icon container; fall back to a document icon when no custom icon is provided -->
                                    <div
                                        class="w-10 h-10 flex items-center justify-center rounded bg-slate-100 text-brand-600"
                                    >
                                        <span v-if="type.icon" v-html="type.icon"></span>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-6 h-6"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M7.5 3.75h6.75L21 9.75v9a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 1-1.5-1.5V5.25A1.5 1.5 0 0 1 6 3.75h1.5z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <h3 class="text-base font-medium leading-6 text-gray-900">
                                        <router-link
                                            :to="{
                                                name: 'create-service-request-from-type',
                                                params: { typeId: type.id },
                                            }"
                                        >
                                            <span class="absolute inset-0" aria-hidden="true" />
                                            {{ type.name }}
                                        </router-link>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">{{ type.description }}</p>
                                </div>
                                <span
                                    class="pointer-events-none text-gray-300 group-hover:text-brand-600"
                                    aria-hidden="true"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-6 h-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m8.25 4.5 7.5 7.5-7.5 7.5"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="displayedCategories.length === 0 && displayedTypes.length === 0"
                            class="text-gray-500 p-6 bg-white rounded shadow"
                        >
                            No categories or types found.
                        </div>
                    </div>
                </main>
            </Page>
        </div>
    </div>
</template>
