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
    import { ArrowLeftIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/16/solid';
    import { onMounted, ref, watch } from 'vue';
    import { useServiceRequestConversation } from '../../composables/useServiceRequestConversation.js';

    const props = defineProps({
        title: { type: String, required: true },
        aiResolved: { type: Boolean, default: false },
        serviceRequestId: { type: String, default: null },
        websocketsConfig: { type: Object, default: null },
        authEndpoint: { type: String, default: null },
    });

    defineEmits(['back']);

    const { eligible, agentName, status, error, checkEligibility, requestConversation, cleanup } =
        useServiceRequestConversation(props.websocketsConfig, props.authEndpoint);

    const countdown = ref(300);
    let countdownInterval = null;

    onMounted(() => {
        if (!props.aiResolved && props.serviceRequestId) {
            checkEligibility(props.serviceRequestId);
        }
    });

    function startConversation() {
        requestConversation(props.serviceRequestId);
        startCountdown();
    }

    function startCountdown() {
        countdown.value = 300;
        countdownInterval = setInterval(() => {
            countdown.value--;
            if (countdown.value <= 0) {
                clearInterval(countdownInterval);
                countdownInterval = null;
                if (status.value === 'queued') {
                    status.value = 'expired';
                    cleanup();
                }
            }
        }, 1000);
    }

    watch(status, (newStatus) => {
        if (['accepted', 'declined', 'expired'].includes(newStatus) && countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    });

    function formatCountdown(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }
</script>

<template>
    <div class="flex-1 flex flex-col items-center justify-center px-8 py-10 text-center gap-5">
        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <div class="flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ aiResolved ? 'Issue Resolved' : 'Request Submitted' }}
            </h3>
            <p class="text-sm text-gray-500 leading-relaxed">
                <template v-if="aiResolved">
                    Your issue
                    <span class="font-medium text-gray-700">"{{ title }}"</span>
                    has been resolved. If you need further assistance, you can submit a new request.
                </template>
                <template v-else>
                    Your service request
                    <span class="font-medium text-gray-700">"{{ title }}"</span>
                    has been received. Our team will get back to you soon.
                </template>
            </p>
        </div>

        <!-- Checking availability -->
        <template v-if="!aiResolved && status === 'checking'">
            <div class="w-full border-t border-gray-100 pt-4 flex items-center justify-center gap-2">
                <svg
                    class="animate-spin h-4 w-4 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-sm text-gray-500">Checking agent availability...</span>
            </div>
        </template>

        <!-- Conversation offer -->
        <template v-if="!aiResolved && eligible && status === 'idle'">
            <div class="w-full border-t border-gray-100 pt-4">
                <p class="text-sm text-gray-500 mb-3">An agent may be available to chat with you now.</p>
                <button
                    @click="startConversation"
                    class="flex items-center justify-center gap-2 mx-auto px-5 py-2.5 rounded bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium transition-all shadow-sm"
                >
                    <ChatBubbleLeftRightIcon class="w-4 h-4" />
                    Request Live Chat
                </button>
            </div>
        </template>

        <!-- Waiting for agent -->
        <template v-if="status === 'queued'">
            <div class="w-full border-t border-gray-100 pt-4 flex flex-col items-center gap-3">
                <div class="flex items-center gap-2">
                    <svg
                        class="animate-spin h-4 w-4 text-brand-500"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        ></path>
                    </svg>
                    <span class="text-sm text-gray-500">Waiting for an agent to accept...</span>
                </div>
                <span class="text-xs text-gray-400 tabular-nums">{{ formatCountdown(countdown) }}</span>
            </div>
        </template>

        <!-- Accepted -->
        <template v-if="status === 'accepted'">
            <div class="w-full border-t border-gray-100 pt-4 flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <ChatBubbleLeftRightIcon class="w-5 h-5 text-green-600" />
                </div>
                <p class="text-sm text-gray-700 font-medium">Agent connected! Your chat is ready.</p>
            </div>
        </template>

        <!-- Declined or Expired -->
        <template v-if="status === 'declined' || status === 'expired'">
            <div class="w-full border-t border-gray-100 pt-4 flex flex-col items-center gap-2">
                <p class="text-sm text-gray-500">
                    No agents are available right now. Our team will follow up on your request.
                </p>
            </div>
        </template>

        <!-- Error -->
        <template v-if="status === 'error'">
            <div class="w-full border-t border-gray-100 pt-4 flex flex-col items-center gap-2">
                <p class="text-sm text-gray-500">{{ error }}</p>
            </div>
        </template>

        <button
            @click="$emit('back')"
            class="mt-2 flex items-center gap-2 px-5 py-2.5 rounded bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium transition-all shadow-sm"
        >
            <ArrowLeftIcon class="w-4 h-4" />
            Back to Assistant Chat
        </button>
    </div>
</template>
