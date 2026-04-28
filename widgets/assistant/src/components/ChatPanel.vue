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
    import { ref } from 'vue';
    import { useAssistantChat } from '../composables/useAssistantChat.js';
    import ChatHeader from './ChatHeader.vue';
    import ChatInput from './ChatInput.vue';
    import ChatMessages from './ChatMessages.vue';
    import ChatSignIn from './ChatSignIn.vue';
    import ServiceRequestView from './ServiceRequestView.vue';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
        sendMessageUrl: { type: String, required: true },
        websocketsConfig: { type: Object, required: true },
        isAuthenticated: { type: Boolean, default: false },
        portalServiceManagement: { type: Boolean, default: false },
        authenticateRequestUrl: { type: String, default: null },
        serviceRequestTypesUrl: { type: String, default: null },
    });

    const emit = defineEmits(['close', 'authenticated']);

    // 'chat' | 'sign-in' | 'service-request'
    const currentView = ref('chat');
    const pendingView = ref(null);

    const { messages, isSending, isAssistantResponding, sendMessage, setAuthenticated } = useAssistantChat(
        props.sendMessageUrl,
        props.websocketsConfig,
        props.isAuthenticated,
    );

    function onOpenServiceRequest() {
        if (props.isAuthenticated) {
            currentView.value = 'service-request';
        } else {
            pendingView.value = 'service-request';
            currentView.value = 'sign-in';
        }
    }

    function onBack() {
        currentView.value = 'chat';
        pendingView.value = null;
    }

    function onAuthenticated(token) {
        setAuthenticated();
        emit('authenticated', token);
        currentView.value = pendingView.value ?? 'chat';
        pendingView.value = null;
    }

    const welcomeMessage =
        'Hi there, I am your support assistant. I can help you find information and troubleshoot issues. How can I assist you today?';
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-100 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-75 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-4"
    >
        <div
            v-if="props.isOpen"
            class="mb-4 w-[400px] max-w-full h-[650px] max-h-full bg-white rounded-lg shadow-2xl flex flex-col overflow-hidden ring-1 ring-brand-950/5 backdrop-blur-sm origin-bottom-right"
        >
            <ChatHeader
                :service-request-enabled="!!serviceRequestTypesUrl && portalServiceManagement"
                :current-view="currentView"
                @close="emit('close')"
                @open-service-request="onOpenServiceRequest"
                @back="onBack"
            />

            <ChatSignIn
                v-if="currentView === 'sign-in'"
                :authenticate-request-url="authenticateRequestUrl"
                @authenticated="onAuthenticated"
                @cancel="onBack"
            />

            <ServiceRequestView
                v-else-if="currentView === 'service-request'"
                :service-request-types-url="serviceRequestTypesUrl"
                @back="onBack"
            />

            <template v-else>
                <ChatMessages :messages="messages" :welcome-message="welcomeMessage" :is-open="props.isOpen" />

                <ChatInput :disabled="isSending || isAssistantResponding" @send="sendMessage" />
            </template>
        </div>
    </Transition>
</template>
