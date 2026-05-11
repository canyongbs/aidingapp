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
    import { FormKitSchema } from '@formkit/vue';
    import { ArrowLeftIcon } from '@heroicons/vue/16/solid';
    import { ArrowRightIcon } from '@heroicons/vue/20/solid';
    import { computed, provide, ref, watch } from 'vue';

    const props = defineProps({
        step: { type: Object, required: true },
        stepIndex: { type: Number, required: true },
        totalSteps: { type: Number, required: true },
        selectedType: { type: Object, required: true },
        uploadUrl: { type: String, default: '' },
        isSubmitting: { type: Boolean, default: false },
        submitError: { type: String, default: null },
        fieldErrors: { type: Object, default: () => ({}) },
    });

    const emit = defineEmits(['back', 'next', 'submit']);

    const isUploadProcessing = ref(false);
    provide('uploadProcessing', isUploadProcessing);

    const isLastStep = computed(() => props.stepIndex === props.totalSteps - 1);
    const formRef = ref(null);
    const isFormValid = ref(true);
    const formNodeReady = ref(false);

    function onFormCreated(node) {
        node.on('count:blocking', ({ payload: count }) => {
            isFormValid.value = count === 0;
        });

        node.on('node:added', () => {
            isFormValid.value = node.ledger.value('blocking') === 0;
        });

        node.settled.then(() => {
            formNodeReady.value = true;
        });
    }

    watch(
        [() => props.fieldErrors, formNodeReady],
        ([errors, ready]) => {
            if (!ready) return;

            const node = formRef.value?.node;
            if (!node || !errors || Object.keys(errors).length === 0) return;

            const fieldNames = new Set((props.step.schema || []).map((s) => s.name).filter(Boolean));

            const stepErrors = {};

            for (const [key, messages] of Object.entries(errors)) {
                if (fieldNames.has(key)) {
                    stepErrors[key] = messages;
                }
            }

            if (Object.keys(stepErrors).length > 0) {
                node.setErrors([], stepErrors);
            }
        },
        { immediate: true },
    );

    function getFieldValues() {
        const node = formRef.value?.node;
        if (!node) return {};

        return node.value ?? {};
    }

    defineExpose({ getFieldValues });

    function onFormSubmit() {
        if (isLastStep.value) {
            emit('submit');
        } else {
            emit('next');
        }
    }

    function validateAndProceed() {
        const formEl = formRef.value?.$el;
        if (formEl) {
            formEl.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    }
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Step header -->
        <div
            class="shrink-0 mx-4 mt-4 mb-1 flex items-center gap-2 px-3 py-2 bg-brand-50 border border-brand-100 rounded"
        >
            <button
                @click="$emit('back')"
                class="shrink-0 text-brand-400 hover:text-brand-600 transition-colors"
                aria-label="Back"
            >
                <ArrowLeftIcon class="w-4 h-4" />
            </button>
            <div class="flex-1 flex items-center gap-2 min-w-0 text-xs">
                <span class="text-brand-700 font-semibold truncate">{{ step.label }}</span>
                <span class="text-brand-300 shrink-0">·</span>
                <span class="text-brand-500 truncate">Step {{ stepIndex + 2 }} of {{ totalSteps + 1 }}</span>
            </div>
        </div>

        <!-- Form fields -->
        <div class="flex-1 overflow-y-auto px-4 py-3 flex flex-col">
            <FormKit ref="formRef" type="form" :actions="false" @submit="onFormSubmit" @node="onFormCreated">
                <FormKitSchema :schema="step.schema" />
            </FormKit>

            <!-- Submit error -->
            <div v-if="submitError" class="px-3 py-2.5 rounded bg-red-50 border border-red-100 mt-2">
                <p class="text-sm text-red-600">{{ submitError }}</p>
            </div>
        </div>

        <!-- Navigation footer -->
        <div class="shrink-0 px-4 pb-4 pt-3 border-t border-gray-100">
            <button
                @click="validateAndProceed"
                :disabled="isUploadProcessing || isSubmitting || !isFormValid"
                :class="[
                    'w-full flex items-center justify-center gap-2 py-2.5 rounded text-sm font-medium transition-all',
                    !isUploadProcessing && !isSubmitting && isFormValid
                        ? 'bg-brand-500 hover:bg-brand-600 text-white shadow-sm'
                        : 'bg-gray-100 text-gray-300 cursor-not-allowed',
                ]"
            >
                <svg
                    v-if="isUploadProcessing || isSubmitting"
                    class="w-4 h-4 animate-spin"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
                {{
                    isUploadProcessing
                        ? 'Uploading…'
                        : isSubmitting
                          ? 'Submitting…'
                          : isLastStep
                            ? 'Submit Service Request'
                            : 'Next'
                }}
                <ArrowRightIcon v-if="!isUploadProcessing && !isSubmitting" class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
