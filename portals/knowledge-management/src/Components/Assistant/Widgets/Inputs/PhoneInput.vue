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
    import { ref, computed } from 'vue';

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

    const countryCode = ref('+1');
    const phoneNumber = ref('');
    const error = ref('');

    const countryCodes = [
        { code: '+1', label: 'US/CA (+1)' },
        { code: '+44', label: 'UK (+44)' },
        { code: '+61', label: 'AU (+61)' },
        { code: '+49', label: 'DE (+49)' },
        { code: '+33', label: 'FR (+33)' },
        { code: '+81', label: 'JP (+81)' },
        { code: '+86', label: 'CN (+86)' },
        { code: '+91', label: 'IN (+91)' },
        { code: '+52', label: 'MX (+52)' },
        { code: '+55', label: 'BR (+55)' },
    ];

    const formattedPhone = computed(() => {
        if (!phoneNumber.value) return '';
        return `${countryCode.value} ${phoneNumber.value}`;
    });

    const isValid = computed(() => {
        if (!phoneNumber.value) return false;
        const digitsOnly = phoneNumber.value.replace(/\D/g, '');
        return digitsOnly.length >= 7 && digitsOnly.length <= 15;
    });

    const submit = () => {
        if (props.required && !phoneNumber.value) {
            error.value = 'Please enter a phone number';
            return;
        }

        if (phoneNumber.value && !isValid.value) {
            error.value = 'Please enter a valid phone number';
            return;
        }

        emit(
            'submit',
            {
                country_code: countryCode.value,
                number: phoneNumber.value,
                formatted: formattedPhone.value,
            },
            formattedPhone.value
        );
    };
</script>

<template>
    <div class="space-y-3">
        <div class="flex gap-2">
            <select
                v-model="countryCode"
                class="w-28 px-2 py-2 text-sm border border-gray-300 rounded-md focus:ring-brand-500 focus:border-brand-500"
            >
                <option v-for="country in countryCodes" :key="country.code" :value="country.code">
                    {{ country.label }}
                </option>
            </select>

            <input
                type="tel"
                v-model="phoneNumber"
                placeholder="Phone number"
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-brand-500 focus:border-brand-500"
                @input="error = ''"
            />
        </div>

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
