<template>
    <div>
        <Bars3Icon @click="openDrawer" class="h-6 w-6 text-gray-700" aria-hidden="true" />

        <Transition name="backdrop">
            <div v-if="visible" class="fixed inset-0 z-40 bg-black bg-opacity-50" @click="closeDrawer"></div>
        </Transition>

        <Transition name="drawer">
            <div v-if="visible" class="fixed top-0 right-0 w-64 h-full bg-white shadow-xl z-50" @click.stop>
                <div class="flex justify-between items-center px-4 py-3 border-b">
                    <h3 class="text-lg font-semibold text-gray-700">Menu</h3>
                    <XMarkIcon @click="closeDrawer" class="h-5 w-5 text-gray-700" />
                </div>

                <div class="p-4 space-y-5">
                    <template v-for="item in visibleMenuItems" :key="item.label">
                        <router-link
                            :to="{ name: item.routeName }"
                            custom
                            v-slot="{ navigate, isActive, isExactActive }"
                        >
                            <a
                                @click="navigate"
                                class="flex items-center font-medium text-sm"
                                :class="[
                                    isActive || isExactActive ? 'text-brand-500' : 'text-gray-700',
                                    'hover:text-brand-500',
                                ]"
                            >
                                {{ item.label }}
                            </a>
                        </router-link>
                    </template>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
    import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { ref } from 'vue';

    const props = defineProps({
        visibleMenuItems: {
            type: Object,
            required: true,
        },
    });

    const visible = ref(false);

    const openDrawer = () => {
        visible.value = true;
    };

    const closeDrawer = () => {
        visible.value = false;
    };
</script>

<style scoped>
    .drawer-enter-active,
    .drawer-leave-active {
        transition:
            transform 0.3s ease-out,
            opacity 0.3s ease-out;
    }

    .drawer-enter-from,
    .drawer-leave-to {
        transform: translateX(100%);
        opacity: 0;
    }

    .drawer-enter-to,
    .drawer-leave-from {
        transform: translateX(0);
        opacity: 1;
    }

    .backdrop-enter-active,
    .backdrop-leave-active {
        transition: opacity 0.3s ease-out;
    }

    .backdrop-enter-from,
    .backdrop-leave-to {
        opacity: 0;
    }

    .backdrop-enter-to,
    .backdrop-leave-from {
        opacity: 1;
    }
</style>
