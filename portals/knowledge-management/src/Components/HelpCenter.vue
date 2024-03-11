<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
import { defineProps } from 'vue';

defineProps({
    categories: {
        type: Object,
        required: true,
    },
    serviceRequests: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="grid gap-4" :class="[serviceRequests?.length > 0 ? 'lg:grid-cols-2' : 'lg:grid-cols-1']">
        <div>
            <h3 class="text-xl">Help Center</h3>
            <div class="mt-4 grid gap-4" :class="[serviceRequests?.length > 0 ? 'lg:grid-cols-1' : 'lg:grid-cols-2']">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="group relative bg-white p-6 rounded shadow"
                >
                    <div class="grid">
                        <div class="flex" :class="[category.icon ? 'justify-between' : 'justify-end']">
                            <div
                                v-if="category.icon"
                                v-html="category.icon"
                                class="pointer-events-none text-primary-600 dark:text-primary-400"
                                aria-hidden="true"
                            ></div>

                            <div
                                class="pointer-events-none text-gray-300 group-hover:text-primary-600 group-hover:dark:text-primary-400"
                                aria-hidden="true"
                            >
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"
                                    />
                                </svg>
                            </div>
                        </div>

                        <div class="w-full">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                <router-link :to="{ name: 'view-category', params: { categoryId: category.id } }">
                                    <span class="absolute inset-0" aria-hidden="true" />
                                    {{ category.name }}
                                </router-link>
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ category.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="serviceRequests?.length > 0">
            <h3 class="text-xl">Service Requests</h3>
            <div class="mt-4 grid gap-y-4">
                <div
                    v-for="serviceRequest in serviceRequests"
                    :key="serviceRequest.id"
                    class="group relative bg-white p-6 rounded shadow"
                >
                    <div class="flex items-center gap-x-3">
                        <span
                            v-if="serviceRequest.icon"
                            v-html="serviceRequest.icon"
                            class="pointer-events-none text-primary-600 dark:text-primary-400"
                            aria-hidden="true"
                        >
                        </span>
                        <div class="grid w-full gap-y-1.5">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                {{ serviceRequest.title }}
                            </h3>
                            <div>
                                <span
                                    class="px-2 py-1 text-sm font-bold text-white rounded"
                                    :style="'background-color: rgb(' + serviceRequest.status_color + ')'"
                                >
                                    {{ serviceRequest.status_name }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">Last Updated: {{ serviceRequest.updated_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
