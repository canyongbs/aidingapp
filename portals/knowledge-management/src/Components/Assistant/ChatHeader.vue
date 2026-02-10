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
    import { ChatBubbleLeftRightIcon, PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { useAuthStore } from '../../Stores/auth.js';
    import { useFeatureStore } from '../../Stores/feature.js';

    const { user } = useAuthStore();
    const { hasServiceManagement } = useFeatureStore();

    defineEmits(['close', 'new-request']);
</script>

<template>
    <div
        class="bg-[linear-gradient(to_right_bottom,rgba(var(--primary-500),1),rgba(var(--primary-800),1))] text-white px-6 py-4 flex items-center justify-between shadow-md shrink-0"
    >
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg">
                <ChatBubbleLeftRightIcon class="w-5 h-5" />
            </div>
            <div class="flex flex-col">
                <h2 class="text-lg font-semibold tracking-tight">Support Assistant</h2>
                <div v-if="hasServiceManagement && user" class="mt-1">
                    <router-link :to="{ name: 'create-service-request' }">
                        <button
                            @click="$emit('close')"
                            class="flex items-center gap-1.5 px-3 py-1.5 font-medium text-xs rounded-lg bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-sm transition-all shadow-sm"
                        >
                            <PlusIcon class="w-3.5 h-3.5" />
                            Open Service Request
                        </button>
                    </router-link>
                </div>
            </div>
        </div>
        <button
            @click="$emit('close')"
            class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
            aria-label="Close chat"
        >
            <XMarkIcon class="w-5 h-5" />
        </button>
    </div>
</template>
