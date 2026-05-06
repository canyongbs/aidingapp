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
    import { computed, ref } from 'vue';
    import { useServiceRequestSubmit } from '../composables/useServiceRequestSubmit.js';
    import CustomFieldsStep from './ServiceRequest/CustomFieldsStep.vue';
    import DetailsStep from './ServiceRequest/DetailsStep.vue';
    import SuccessStep from './ServiceRequest/SuccessStep.vue';
    import TypeSelectStep from './ServiceRequest/TypeSelectStep.vue';

    defineProps({
        serviceRequestTypesUrl: { type: String, required: true },
    });

    defineEmits(['back']);

    const step = ref('type-select');
    const detailsData = ref(null);
    const submittedTitle = ref('');
    const customFormSteps = ref([]);
    const currentCustomStepIndex = ref(0);

    const submitState = ref(null);

    const isSubmitting = computed(() => submitState.value?.isSubmitting ?? false);
    const submitError = ref(null);
    const fieldErrors = ref({});

    function onTypeSelectContinue(data) {
        detailsData.value = data;
        customFormSteps.value = data.formSteps ?? [];

        submitState.value = useServiceRequestSubmit(data.rawData.store_url_base, data.type.id, data.priority);

        step.value = 'details';
    }

    function onDetailsNext() {
        currentCustomStepIndex.value = 0;
        step.value = 'custom-step';
    }

    function onCustomStepBack() {
        if (currentCustomStepIndex.value === 0) {
            step.value = 'details';
        } else {
            currentCustomStepIndex.value--;
        }
    }

    function onCustomStepNext() {
        currentCustomStepIndex.value++;
    }

    function onCustomStepSubmit() {
        doSubmit();
    }

    async function doSubmit() {
        if (!submitState.value) return;

        submitError.value = null;
        fieldErrors.value = {};

        const allCustomFields = {};
        customStepRefs.value.forEach((stepRef) => {
            if (stepRef?.getFieldValues) {
                Object.assign(allCustomFields, stepRef.getFieldValues());
            }
        });

        const titleSnapshot = submitState.value.title;

        const result = await submitState.value.submitForm(allCustomFields, () => {
            submittedTitle.value = titleSnapshot;
            step.value = 'success';
        });

        if (result?.fieldErrors) {
            fieldErrors.value = result.fieldErrors;

            const errorFieldId = Object.keys(result.fieldErrors)[0];
            if (errorFieldId) {
                const errorStepIndex = customFormSteps.value.findIndex((s) =>
                    s.schema?.some((field) => field.name === errorFieldId),
                );
                if (errorStepIndex >= 0) {
                    currentCustomStepIndex.value = errorStepIndex;
                    step.value = 'custom-step';
                }
            }
        } else if (result?.error) {
            submitError.value = result.error;
        }
    }

    const customStepRefs = ref([]);

    function setCustomStepRef(index, el) {
        customStepRefs.value[index] = el;
    }
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <TypeSelectStep
            v-if="step === 'type-select'"
            :service-request-types-url="serviceRequestTypesUrl"
            @continue="onTypeSelectContinue"
        />

        <DetailsStep
            v-if="step === 'details'"
            :selected-type="detailsData.type"
            :selected-priority="detailsData.priority"
            :raw-data="detailsData.rawData"
            :has-custom-steps="customFormSteps.length > 0"
            :submit-state="submitState"
            @back="step = 'type-select'"
            @next="onDetailsNext"
            @success="
                (title) => {
                    submittedTitle = title;
                    step = 'success';
                }
            "
        />

        <CustomFieldsStep
            v-for="(formStep, index) in customFormSteps"
            v-show="step === 'custom-step' && currentCustomStepIndex === index"
            :key="formStep.label"
            :ref="(el) => setCustomStepRef(index, el)"
            :step="formStep"
            :step-index="index"
            :total-steps="customFormSteps.length"
            :selected-type="detailsData.type"
            :upload-url="detailsData.rawData?.upload_url"
            :is-submitting="isSubmitting"
            :submit-error="currentCustomStepIndex === index ? submitError : null"
            :field-errors="fieldErrors"
            @back="onCustomStepBack"
            @next="onCustomStepNext"
            @submit="onCustomStepSubmit"
        />

        <SuccessStep v-if="step === 'success'" :title="submittedTitle" @back="$emit('back')" />
    </div>
</template>
