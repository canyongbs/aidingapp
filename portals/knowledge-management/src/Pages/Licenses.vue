<script setup>
    import { EyeIcon, EyeSlashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { Card } from 'primevue';
    import { onMounted, ref } from 'vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import { consumer } from '../Services/Consumer';

    const productLicenses = ref({});
    const { get } = consumer();
    const loading = ref(true);
    const showLicenseKeys = ref({});

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    async function getProductLicenses() {
        const response = await get(`${props.apiUrl}/product-licenses`);

        if (response.error) {
            throw new Error(response.error);
        }

        return response.data;
    }

    function toggleLicenseKey(id) {
        showLicenseKeys.value[id] = !showLicenseKeys.value[id];
    }

    onMounted(async () => {
        await getProductLicenses()
            .then((response) => {
                productLicenses.value = response;
                loading.value = false;
            })
            .catch((error) => {
                console.error('Error fetching product licenses:', error);
                loading.value = false;
            });
    });
</script>

<template>
    <main class="px-6 bg-gray-50">
        <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <Breadcrumbs :currentCrumb="'Licenses'" />

            <div v-if="loading" class="flex flex-col gap-y-8">
                <div>
                    <div class="h-6 bg-gray-300 rounded w-24 mb-3"></div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div
                            v-for="n in 4"
                            :key="'active-skel-' + n"
                            class="p-7 bg-white text-sm shadow-md rounded-lg border border-gray-200 animate-pulse"
                        >
                            <div class="h-4 bg-gray-300 rounded w-1/2 mb-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/3 mb-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/4 mb-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="h-6 bg-gray-300 rounded w-24 mb-3"></div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div
                            v-for="n in 4"
                            :key="'expired-skel-' + n"
                            class="p-7 bg-white text-sm shadow-md rounded-lg border border-gray-200 animate-pulse"
                        >
                            <div class="h-4 bg-gray-300 rounded w-1/2 mb-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/3 mb-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/4 mb-2"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col gap-y-8">
                <div v-if="productLicenses.activeLicense?.length > 0">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Active</h3>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <Card
                            v-for="activeLicense in productLicenses.activeLicense"
                            :key="activeLicense.id"
                            class="mt-1 bg-white text-sm shadow-md rounded-lg border border-gray-200"
                        >
                            <template #content>
                                <p class="mb-2 font-semibold text-gray-800">
                                    Product Name:
                                    <span class="font-normal text-gray-700">{{ activeLicense.product.name }}</span>
                                </p>
                                <p class="mb-2 font-semibold text-gray-800">
                                    Product Version:
                                    <span class="font-normal text-gray-700">{{ activeLicense.product.version }}</span>
                                </p>
                                <p class="mb-2 font-semibold text-gray-800">
                                    Expiration:
                                    <span class="font-normal text-gray-700">
                                        {{ activeLicense.formatted_expiration_date ?? 'No Expiration' }}
                                    </span>
                                </p>
                                <p class="flex gap-2 font-semibold text-gray-800">
                                    License Key:
                                    <span
                                        class="font-normal text-gray-700 bg-gray-100 px-2 py-1 rounded"
                                        :class="{ 'blur-sm': !showLicenseKeys[activeLicense.id] }"
                                    >
                                        {{ activeLicense.license }}
                                    </span>
                                    <button
                                        @click="toggleLicenseKey(activeLicense.id)"
                                        class="text-gray-500 hover:text-gray-700"
                                    >
                                        <EyeSlashIcon v-if="!showLicenseKeys[activeLicense.id]" class="h-5 w-5" />
                                        <EyeIcon v-else class="h-5 w-5" />
                                    </button>
                                </p>
                            </template>
                        </Card>
                    </div>
                </div>

                <div v-if="productLicenses.expiredLicense?.length > 0">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Expired</h3>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <Card
                            v-for="expiredLicense in productLicenses.expiredLicense"
                            :key="expiredLicense.id"
                            class="mt-1 bg-white text-sm shadow-md rounded-lg border border-gray-200"
                        >
                            <template #content>
                                <p class="mb-2 font-semibold text-gray-800">
                                    Product Name:
                                    <span class="font-normal text-gray-700">{{ expiredLicense.product.name }}</span>
                                </p>
                                <p class="mb-2 font-semibold text-gray-800">
                                    Product Version:
                                    <span class="font-normal text-gray-700">{{ expiredLicense.product.version }}</span>
                                </p>
                                <p class="mb-2 font-semibold text-gray-800">
                                    Expiration:
                                    <span class="font-normal text-gray-700">
                                        {{ expiredLicense.formatted_expiration_date }}
                                    </span>
                                </p>
                                <p class="flex gap-2 font-semibold text-gray-800">
                                    License Key:
                                    <span
                                        class="font-normal text-gray-700 bg-gray-100 px-2 py-1 rounded"
                                        :class="{ 'blur-sm': !showLicenseKeys[expiredLicense.id] }"
                                    >
                                        {{ expiredLicense.license }}
                                    </span>
                                    <button
                                        @click="toggleLicenseKey(expiredLicense.id)"
                                        class="text-gray-500 hover:text-gray-700"
                                    >
                                        <EyeSlashIcon v-if="!showLicenseKeys[expiredLicense.id]" class="h-5 w-5" />
                                        <EyeIcon v-else class="h-5 w-5" />
                                    </button>
                                </p>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
            <div
                v-if="!loading && !productLicenses.activeLicense.length && !productLicenses.expiredLicense.length"
                class="p-3 flex items-start gap-2"
            >
                <XMarkIcon class="h-5 w-5 text-gray-400" />

                <p class="text-gray-600 text-sm font-medium">No license found.</p>
            </div>
        </div>
    </main>
</template>
