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
    import { computed } from 'vue';
    import FieldInputWidget from './Widgets/FieldInputWidget.vue';
    import TypeSelectorWidget from './Widgets/TypeSelectorWidget.vue';

    const props = defineProps({
        actionType: {
            type: String,
            required: true,
        },
        params: {
            type: Object,
            required: true,
        },
    });

    const emit = defineEmits(['submit', 'cancel']);

    const isLoading = computed(() => props.actionType === 'loading');

    const widgetComponent = computed(() => {
        switch (props.actionType) {
            case 'select_service_request_type':
                return TypeSelectorWidget;
            case 'render_field_input':
                return FieldInputWidget;
            default:
                return null;
        }
    });
</script>

<template>
    <div class="px-4 pb-4">
        <!-- Loading state -->
        <div v-if="isLoading" class="bg-gray-50 rounded-lg border border-gray-200 p-6 flex items-center justify-center">
            <svg
                class="animate-spin h-5 w-5 text-brand-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
        </div>
        <!-- Widget component -->
        <component
            v-else-if="widgetComponent"
            :is="widgetComponent"
            :params="params"
            @submit="emit('submit', $event)"
            @cancel="emit('cancel')"
        />
    </div>
</template>
