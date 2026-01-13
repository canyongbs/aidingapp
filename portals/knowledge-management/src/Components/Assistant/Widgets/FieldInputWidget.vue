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
    import AddressInput from './Inputs/AddressInput.vue';
    import DateInput from './Inputs/DateInput.vue';
    import FileUploadInput from './Inputs/FileUploadInput.vue';
    import PhoneInput from './Inputs/PhoneInput.vue';
    import RadioInput from './Inputs/RadioInput.vue';
    import SelectInput from './Inputs/SelectInput.vue';

    const props = defineProps({
        params: {
            type: Object,
            required: true,
        },
    });

    const emit = defineEmits(['submit', 'cancel']);

    const fieldId = computed(() => props.params.field_id);
    const fieldConfig = computed(() => props.params.field_config || {});

    const inputComponent = computed(() => {
        switch (fieldConfig.value.type) {
            case 'select':
                return SelectInput;
            case 'radio':
                return RadioInput;
            case 'date':
                return DateInput;
            case 'phone':
                return PhoneInput;
            case 'address':
                return AddressInput;
            case 'upload':
                return FileUploadInput;
            default:
                return null;
        }
    });

    const handleSubmit = (value, displayText) => {
        emit('submit', {
            type: 'field_response',
            field_id: fieldId.value,
            value,
            display_text: displayText,
        });
    };
</script>

<template>
    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
        <div class="p-3 border-b border-gray-200 bg-white">
            <h3 class="text-sm font-medium text-gray-900">
                {{ fieldConfig.label }}
                <span v-if="fieldConfig.required" class="text-red-500">*</span>
            </h3>
        </div>

        <div class="p-3">
            <component
                v-if="inputComponent"
                :is="inputComponent"
                :field-id="fieldId"
                :config="fieldConfig.config || {}"
                :required="fieldConfig.required"
                :label="fieldConfig.label"
                @submit="handleSubmit"
                @cancel="emit('cancel')"
            />

            <div v-else class="text-sm text-gray-500">Unsupported field type: {{ fieldConfig.type }}</div>
        </div>
    </div>
</template>
