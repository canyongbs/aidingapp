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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
    import { computed, ref } from 'vue';

    const props = defineProps({
        fieldId: {
            type: String,
            required: true,
        },
        config: {
            type: Object,
            default: () => ({}),
        },
        required: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: '',
        },
    });

    const emit = defineEmits(['submit', 'cancel']);

    const email = ref('');
    const error = ref('');

    const isValid = computed(() => {
        if (!email.value) return false;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email.value);
    });

    const submit = () => {
        if (props.required && !email.value) {
            error.value = 'Please enter an email address';
            return;
        }

        if (email.value && !isValid.value) {
            error.value = 'Please enter a valid email address';
            return;
        }

        emit('submit', email.value, email.value);
    };
</script>

<template>
    <div class="space-y-3">
        <input
            type="email"
            v-model="email"
            placeholder="email@example.com"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-brand-500 focus:border-brand-500"
            @input="error = ''"
        />

        <p v-if="error" class="text-xs text-red-600">{{ error }}</p>

        <div class="flex gap-2">
            <button
                @click="submit"
                :disabled="required && !isValid"
                class="flex-1 px-3 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Submit
            </button>
            <button
                @click="emit('cancel')"
                class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors"
            >
                Cancel
            </button>
        </div>
    </div>
</template>
