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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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
    import { TicketIcon } from '@heroicons/vue/16/solid';
    import { ArrowLeftIcon, ChatBubbleLeftRightIcon, XMarkIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({
        serviceRequestEnabled: { type: Boolean, default: false },
        currentView: { type: String, default: 'chat' },
    });

    defineEmits(['close', 'open-service-request', 'back']);

    const viewTitles = {
        'sign-in': 'Sign In',
        'service-request': 'Open Service Request',
    };
</script>

<template>
    <div class="bg-brand-500 text-white px-4 py-3.5 flex items-center justify-between shadow-md shrink-0 gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <button
                v-if="currentView !== 'chat'"
                @click="$emit('back')"
                class="shrink-0 text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-lg p-2.5"
                aria-label="Go back"
            >
                <ArrowLeftIcon class="w-5 h-5" />
            </button>

            <div v-else class="shrink-0 bg-white/20 p-2.5 rounded-lg">
                <ChatBubbleLeftRightIcon class="w-5 h-5" />
            </div>

            <div class="flex flex-col min-w-0 gap-1">
                <h2 class="text-sm font-semibold tracking-tight truncate leading-tight">
                    {{ currentView === 'chat' ? 'Support Assistant' : viewTitles[currentView] }}
                </h2>

                <button
                    v-if="currentView === 'chat' && serviceRequestEnabled"
                    @click="$emit('open-service-request')"
                    class="self-start flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/15 hover:bg-white/25 text-white/90 hover:text-white text-xs font-medium transition-all leading-none border border-white/20"
                >
                    <TicketIcon class="w-3 h-3 shrink-0" />
                    Open Service Request
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button
                @click="$emit('close')"
                class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                aria-label="Close chat"
            >
                <XMarkIcon class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>
