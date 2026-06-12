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
    import { ArrowLeftIcon, ArrowRightIcon } from '@heroicons/vue/16/solid';
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    import BaseButton from '../../../../../resources/js/components/BaseButton.vue';
    import LoadingSpinner from '../../../../../resources/js/components/LoadingSpinner.vue';
    import { getAuthHeaders } from '../../utils/token.js';

    const props = defineProps({
        generateQuestionUrl: { type: String, required: true },
        formData: { type: Object, required: true },
        previousQuestionsAndAnswers: { type: Array, default: () => [] },
        questionNumber: { type: Number, required: true },
        selectedType: { type: Object, required: true },
        cachedQuestion: { type: Object, default: null },
        initialAnswer: { type: String, default: '' },
        isFinalStep: { type: Boolean, default: false },
        isSubmitting: { type: Boolean, default: false },
    });

    const emit = defineEmits(['back', 'next', 'question-generated']);

    const isLoading = ref(false);
    const loadError = ref(null);
    const questionField = ref(null);
    const formRef = ref(null);
    const isFormValid = ref(false);

    onMounted(() => {
        if (props.cachedQuestion) {
            questionField.value = props.cachedQuestion;

            if (props.initialAnswer) {
                prefillAnswer();
            }
        } else if (!questionField.value) {
            fetchQuestion();
        }
    });

    function prefillAnswer() {
        const waitForNode = setInterval(() => {
            const node = formRef.value?.node;
            if (node && questionField.value) {
                node.input({ [questionField.value.name]: props.initialAnswer });
                clearInterval(waitForNode);
            }
        }, 50);
    }

    async function fetchQuestion() {
        isLoading.value = true;
        loadError.value = null;

        try {
            const url = props.generateQuestionUrl.replace('__TYPE__', props.selectedType.id);

            const response = await axios.post(
                url,
                {
                    title: props.formData.title,
                    description: props.formData.description,
                    priority_id: props.formData.priority_id,
                    custom_fields: props.formData.custom_fields,
                    previous_questions_and_answers: props.previousQuestionsAndAnswers,
                    question_number: props.questionNumber,
                },
                { headers: getAuthHeaders() },
            );

            questionField.value = response.data.field;
            emit('question-generated', response.data.field);
        } catch (error) {
            loadError.value = 'Failed to generate question. Please try again.';
        } finally {
            isLoading.value = false;
        }
    }

    function onFormCreated(node) {
        node.on('count:blocking', ({ payload: count }) => {
            isFormValid.value = count === 0;
        });

        node.on('node:added', () => {
            isFormValid.value = node.ledger.value('blocking') === 0;
        });
    }

    function getAnswer() {
        if (!questionField.value || !formRef.value?.node) {
            return null;
        }

        const values = formRef.value.node.value ?? {};
        const encryptedQuestion = questionField.value.name;
        const answer = values[encryptedQuestion] ?? '';

        return { encrypted_question: encryptedQuestion, answer };
    }

    defineExpose({ getAnswer });

    function validateAndProceed() {
        const formEl = formRef.value?.$el;
        if (formEl) {
            formEl.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    }

    function onFormSubmit() {
        emit('next');
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
                <span class="text-brand-700 font-semibold truncate">Clarifying Questions</span>
                <span class="text-brand-300 shrink-0">·</span>
                <span class="text-brand-500 truncate">Question {{ questionNumber }} of 3</span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto px-4 py-3 flex flex-col">
            <!-- Loading state -->
            <div v-if="isLoading" class="flex-1 flex items-center justify-center text-gray-400">
                <LoadingSpinner label="Generating question…" size="lg" />
            </div>

            <!-- Error state -->
            <div v-else-if="loadError" class="flex-1 flex flex-col items-center justify-center gap-3">
                <div class="px-3 py-2.5 rounded bg-red-50 border border-red-100">
                    <p class="text-sm text-red-600">{{ loadError }}</p>
                </div>
                <button
                    @click="fetchQuestion"
                    class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors"
                >
                    Try again
                </button>
            </div>

            <!-- Question form -->
            <div v-else-if="questionField">
                <FormKit ref="formRef" type="form" :actions="false" @submit="onFormSubmit" @node="onFormCreated">
                    <FormKitSchema :schema="[questionField]" />
                </FormKit>
            </div>
        </div>

        <!-- Navigation footer -->
        <div v-if="!isLoading && !loadError" class="shrink-0 px-4 pb-4 pt-3 border-t border-gray-100">
            <BaseButton
                @click="validateAndProceed"
                :disabled="!isFormValid"
                :loading="isSubmitting"
                loading-text="Submitting…"
                :icon="ArrowRightIcon"
                icon-position="after"
                size="lg"
                class="w-full"
            >
                {{ isFinalStep ? 'Submit Service Request' : 'Next' }}
            </BaseButton>
        </div>
    </div>
</template>
