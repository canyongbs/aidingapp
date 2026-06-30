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
    import { ArrowLeftIcon } from '@heroicons/vue/16/solid';
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    import BaseButton from '../../../../../resources/js/components/BaseButton.vue';
    import LoadingSpinner from '../../../../../resources/js/components/LoadingSpinner.vue';
    import { useMarkdown } from '../../composables/useMarkdown.js';
    import { getAuthHeaders } from '../../utils/token.js';

    const { renderMarkdown } = useMarkdown();

    const props = defineProps({
        evaluateAiResolutionUrl: { type: String, required: true },
        formData: { type: Object, required: true },
        questions: { type: Object, default: () => ({}) },
        selectedType: { type: Object, required: true },
        isSubmitting: { type: Boolean, default: false },
        preloadedResolution: { type: Promise, default: null },
    });

    const emit = defineEmits(['back', 'resolved', 'declined', 'skip']);

    const isLoading = ref(true);
    const proposedAnswer = ref(null);
    const confidenceScore = ref(null);
    const encryptedProposedAnswer = ref(null);
    const submittingAction = ref(null);

    onMounted(() => {
        evaluateResolution();
    });

    async function evaluateResolution() {
        isLoading.value = true;

        try {
            let data;

            if (props.preloadedResolution) {
                data = await props.preloadedResolution;
            }

            if (!data) {
                const url = props.evaluateAiResolutionUrl.replace('__TYPE__', props.selectedType.id);

                const response = await axios.post(
                    url,
                    {
                        title: props.formData.title,
                        description: props.formData.description,
                        priority_id: props.formData.priority_id,
                        custom_fields: props.formData.custom_fields,
                        questions: props.questions,
                    },
                    { headers: getAuthHeaders() },
                );

                data = response.data;
            }

            if (data.is_ai_resolution_available) {
                proposedAnswer.value = data.proposed_answer;
                confidenceScore.value = data.confidence_score;
                encryptedProposedAnswer.value = data.encrypted_proposed_answer;
            } else {
                emit('skip');
            }
        } catch {
            emit('skip');
        } finally {
            isLoading.value = false;
        }
    }

    function onAccept() {
        submittingAction.value = 'accepted';
        emit('resolved', {
            confidenceScore: confidenceScore.value,
            encryptedProposedAnswer: encryptedProposedAnswer.value,
        });
    }

    function onDecline() {
        submittingAction.value = 'declined';
        emit('declined', {
            confidenceScore: confidenceScore.value,
            encryptedProposedAnswer: encryptedProposedAnswer.value,
        });
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
                <span class="text-brand-700 font-semibold truncate">AI Resolution</span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto px-4 py-3 flex flex-col">
            <!-- Loading state -->
            <div
                v-if="isLoading || (!proposedAnswer && isSubmitting)"
                class="flex-1 flex items-center justify-center text-gray-400"
            >
                <LoadingSpinner
                    :label="isSubmitting ? 'Submitting your request…' : 'Evaluating your request…'"
                    size="lg"
                />
            </div>

            <!-- Resolution available -->
            <div v-else-if="proposedAnswer" class="flex flex-col gap-4">
                <p class="text-sm text-gray-600">
                    Based on the information you provided, we may be able to resolve your request immediately:
                </p>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <div
                        class="prose prose-sm max-w-none text-sm text-gray-800 leading-relaxed prose-p:my-2 prose-p:first:mt-0 prose-p:last:mb-0 prose-ul:my-1 prose-ol:my-1 prose-li:my-0 prose-headings:mt-2 prose-headings:mb-1 prose-h1:text-2xl prose-h2:text-xl prose-h3:text-lg prose-h4:text-base prose-hr:my-2 prose-pre:my-2 prose-blockquote:my-2"
                        v-html="renderMarkdown(proposedAnswer)"
                    ></div>
                </div>

                <p class="text-sm text-gray-500">Did this resolve your issue?</p>
            </div>
        </div>

        <!-- Action footer -->
        <div v-if="!isLoading && proposedAnswer" class="shrink-0 px-4 pb-4 pt-3 border-t border-gray-100 space-y-2">
            <BaseButton
                @click="onAccept"
                :loading="isSubmitting && submittingAction === 'accepted'"
                :disabled="isSubmitting && submittingAction !== 'accepted'"
                color="success"
                size="lg"
                loading-text="Submitting…"
                class="w-full"
            >
                Yes, this resolved my issue
            </BaseButton>
            <BaseButton
                @click="onDecline"
                :loading="isSubmitting && submittingAction === 'declined'"
                :disabled="isSubmitting && submittingAction !== 'declined'"
                color="gray"
                size="lg"
                loading-text="Submitting…"
                class="w-full"
            >
                No, submit my request
            </BaseButton>
        </div>
    </div>
</template>
