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
    import { useServiceRequestWizard } from '../composables/useServiceRequestWizard.js';
    import AiResolutionStep from './ServiceRequest/AiResolutionStep.vue';
    import CustomFieldsStep from './ServiceRequest/CustomFieldsStep.vue';
    import DetailsStep from './ServiceRequest/DetailsStep.vue';
    import QuestionStep from './ServiceRequest/QuestionStep.vue';
    import SuccessStep from './ServiceRequest/SuccessStep.vue';
    import TypeSelectStep from './ServiceRequest/TypeSelectStep.vue';

    defineProps({
        serviceRequestTypesUrl: { type: String, required: true },
        websocketsConfig: { type: Object, default: null },
        authEndpoint: { type: String, default: null },
    });

    defineEmits(['back', 'conversation-active']);

    const {
        step,
        detailsData,
        submittedTitle,
        customFormSteps,
        currentCustomStepIndex,
        submitState,
        isSubmitting,
        submitError,
        fieldErrors,
        serviceRequestId,
        serviceRequestNumber,
        aiClarificationEnabled,
        aiResolutionEnabled,
        questionsAndAnswers,
        generatedQuestions,
        wasAiResolved,
        hasNextStep,
        formDataForAi,
        questionsPayload,
        onTypeSelectContinue,
        onDetailsNext,
        onCustomStepBack,
        onCustomStepNext,
        onCustomStepSubmit,
        onQuestionNext,
        onQuestionBack,
        onAiResolutionBack,
        onAiResolutionResolved,
        onAiResolutionDeclined,
        onAiResolutionSkip,
        setCustomStepRef,
        setQuestionStepRef,
        cacheGeneratedQuestion,
    } = useServiceRequestWizard();
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
            :has-next-step="hasNextStep"
            :submit-state="submitState"
            @back="step = 'type-select'"
            @next="onDetailsNext"
            @success="
                (title) => {
                    submittedTitle = title;
                    serviceRequestId = submitState?.serviceRequestId ?? null;
                    serviceRequestNumber = submitState?.serviceRequestNumber ?? null;
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
            :is-final-step="index === customFormSteps.length - 1 && !aiClarificationEnabled && !aiResolutionEnabled"
            :submit-error="currentCustomStepIndex === index ? submitError : null"
            :field-errors="fieldErrors"
            @back="onCustomStepBack"
            @next="onCustomStepNext"
            @submit="onCustomStepSubmit"
        />

        <QuestionStep
            v-if="step === 'question-1' && aiClarificationEnabled"
            :ref="(el) => setQuestionStepRef(0, el)"
            :generate-question-url="detailsData.rawData.generate_question_url_base"
            :form-data="formDataForAi"
            :previous-questions-and-answers="[]"
            :question-number="1"
            :selected-type="detailsData.type"
            :cached-question="generatedQuestions[0]"
            :initial-answer="questionsAndAnswers[0]?.answer ?? ''"
            @back="onQuestionBack(1)"
            @next="onQuestionNext(1)"
            @question-generated="cacheGeneratedQuestion(1, $event)"
        />

        <QuestionStep
            v-if="step === 'question-2' && aiClarificationEnabled"
            :ref="(el) => setQuestionStepRef(1, el)"
            :generate-question-url="detailsData.rawData.generate_question_url_base"
            :form-data="formDataForAi"
            :previous-questions-and-answers="questionsAndAnswers.slice(0, 1)"
            :question-number="2"
            :selected-type="detailsData.type"
            :cached-question="generatedQuestions[1]"
            :initial-answer="questionsAndAnswers[1]?.answer ?? ''"
            @back="onQuestionBack(2)"
            @next="onQuestionNext(2)"
            @question-generated="cacheGeneratedQuestion(2, $event)"
        />

        <QuestionStep
            v-if="step === 'question-3' && aiClarificationEnabled"
            :ref="(el) => setQuestionStepRef(2, el)"
            :generate-question-url="detailsData.rawData.generate_question_url_base"
            :form-data="formDataForAi"
            :previous-questions-and-answers="questionsAndAnswers.slice(0, 2)"
            :question-number="3"
            :selected-type="detailsData.type"
            :cached-question="generatedQuestions[2]"
            :initial-answer="questionsAndAnswers[2]?.answer ?? ''"
            :is-final-step="!aiResolutionEnabled"
            :is-submitting="isSubmitting"
            @back="onQuestionBack(3)"
            @next="onQuestionNext(3)"
            @question-generated="cacheGeneratedQuestion(3, $event)"
        />

        <AiResolutionStep
            v-if="step === 'ai-resolution' && aiResolutionEnabled"
            :evaluate-ai-resolution-url="detailsData.rawData.evaluate_ai_resolution_url_base"
            :form-data="formDataForAi"
            :questions="questionsPayload"
            :selected-type="detailsData.type"
            :is-submitting="isSubmitting"
            @back="onAiResolutionBack"
            @resolved="onAiResolutionResolved"
            @declined="onAiResolutionDeclined"
            @skip="onAiResolutionSkip"
        />

        <SuccessStep
            v-if="step === 'success'"
            :title="submittedTitle"
            :service-request-number="serviceRequestNumber"
            :ai-resolved="wasAiResolved"
            :service-request-id="serviceRequestId"
            :websockets-config="websocketsConfig"
            :auth-endpoint="authEndpoint"
            @back="$emit('back')"
            @conversation-active="$emit('conversation-active', $event)"
        />
    </div>
</template>
