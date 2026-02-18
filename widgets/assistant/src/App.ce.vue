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
    import { ref } from 'vue';
    import ChatPanel from './components/ChatPanel.vue';
    import ChatToggleButton from './components/ChatToggleButton.vue';

    const props = defineProps({
        sendMessageUrl: { type: String, required: true },
        websocketsConfig: { type: Object, required: true },
        primaryColor: { type: Object, required: true },
        rounding: { type: String, default: 'md' },
        isAuthenticated: { type: Boolean, default: false },
        portalServiceManagement: { type: Boolean, default: false },
    });

    const isOpen = ref(false);

    const toggleChat = () => {
        isOpen.value = !isOpen.value;
    };

    window.addEventListener('assistant:close', () => {
        isOpen.value = false;
    });

    const setPrimaryColor = () => {
        if (!props.primaryColor || typeof props.primaryColor !== 'object') {
            return;
        }

        // Set all shades from the color palette
        Object.entries(props.primaryColor).forEach(([shade, value]) => {
            document.documentElement.style.setProperty(`--primary-${shade}`, value);
        });
    };

    const setRounding = () => {
        const roundingValues = {
            none: {
                sm: '0px',
                default: '0px',
                md: '0px',
                lg: '0px',
                full: '0px',
            },
            sm: {
                sm: '0.125rem',
                default: '0.25rem',
                md: '0.375rem',
                lg: '0.5rem',
                full: '9999px',
            },
            md: {
                sm: '0.25rem',
                default: '0.375rem',
                md: '0.5rem',
                lg: '0.75rem',
                full: '9999px',
            },
            lg: {
                sm: '0.375rem',
                default: '0.5rem',
                md: '0.75rem',
                lg: '1rem',
                full: '9999px',
            },
            full: {
                sm: '9999px',
                default: '9999px',
                md: '9999px',
                lg: '9999px',
                full: '9999px',
            },
        };

        const selectedRounding = roundingValues[props.rounding] || roundingValues.md;

        document.documentElement.style.setProperty('--rounding-sm', selectedRounding.sm);
        document.documentElement.style.setProperty('--rounding', selectedRounding.default);
        document.documentElement.style.setProperty('--rounding-md', selectedRounding.md);
        document.documentElement.style.setProperty('--rounding-lg', selectedRounding.lg);
        document.documentElement.style.setProperty('--rounding-full', selectedRounding.full);
    };

    setPrimaryColor();
    setRounding();
</script>

<template>
    <div class="fixed bottom-4 end-4 z-50 flex flex-col items-end max-h-[calc(100vh-2rem)] max-w-[calc(100vw-2rem)]">
        <ChatPanel
            :is-open="isOpen"
            :send-message-url="sendMessageUrl"
            :websockets-config="websocketsConfig"
            :is-authenticated="isAuthenticated"
            :portal-service-management="portalServiceManagement"
            @close="toggleChat"
        />

        <ChatToggleButton :is-open="isOpen" @toggle="toggleChat" />
    </div>
</template>
