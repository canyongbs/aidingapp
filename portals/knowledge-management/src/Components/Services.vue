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
    <main class="px-6 bg-gray-50" style="height: calc(100vh - 160px)">
        <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <div class="grid gap-4" :class="[serviceRequests?.length > 0 ? 'lg:grid-cols-2' : 'lg:grid-cols-1']">
                <div v-if="serviceRequests?.length > 0">
                    <h3 class="text-xl">Service Requests</h3>
                    <div
                        class="mt-4 overflow-hidden rounded bg-gray-200 shadow-sm ring-1 ring-black/5 grid gap-px divide-y-0"
                    >
                        <div
                            v-for="serviceRequest in serviceRequests"
                            :key="serviceRequest.id"
                            class="group relative bg-white p-6 focus-within:bg-gray-50"
                        >
                            <div class="grid">
                                <div class="flex" :class="[serviceRequest.icon ? 'justify-between' : 'justify-end']">
                                    <div
                                        v-if="serviceRequest.icon"
                                        v-html="serviceRequest.icon"
                                        class="pointer-events-none absolute top-6 text-brand-700"
                                        aria-hidden="true"
                                    ></div>

                                    <div
                                        class="pointer-events-none absolute right-6 top-6 text-gray-300 transition group-hover:text-brand-500"
                                        aria-hidden="true"
                                    >
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div class="w-full mt-8">
                                    <router-link
                                        :to="{
                                            name: 'view-service-request',
                                            params: { serviceRequestId: serviceRequest.id },
                                        }"
                                    >
                                        <h3 class="text-base font-semibold leading-6 text-gray-900">
                                            {{ serviceRequest.title }}
                                        </h3>
                                        <div class="mt-2">
                                            <span
                                                class="px-2 py-1 text-sm font-bold text-white rounded"
                                                :style="'background-color: rgb(' + serviceRequest.status_color + ')'"
                                            >
                                                Status: {{ serviceRequest.status_name }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">
                                            Last Updated: {{ serviceRequest.updated_at }}
                                        </p>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
