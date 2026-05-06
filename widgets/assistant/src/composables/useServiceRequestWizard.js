/*
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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
*/
import { computed, ref } from 'vue';
import { useServiceRequestSubmit } from './useServiceRequestSubmit.js';

export function useServiceRequestWizard() {
    const step = ref('type-select');
    const detailsData = ref(null);
    const submittedTitle = ref('');
    const customFormSteps = ref([]);
    const currentCustomStepIndex = ref(0);

    const submitState = ref(null);

    const isSubmitting = computed(() => submitState.value?.isSubmitting ?? false);
    const submitError = ref(null);
    const fieldErrors = ref({});

    const aiClarificationEnabled = ref(false);
    const aiResolutionEnabled = ref(false);
    const questionsAndAnswers = ref([]);
    const aiResolutionResult = ref(null);
    const wasAiResolved = ref(false);

    const questionStepRefs = ref([null, null, null]);
    const generatedQuestions = ref([null, null, null]);
    const customStepRefs = ref([]);

    function onTypeSelectContinue(data) {
        detailsData.value = data;
        customFormSteps.value = data.formSteps ?? [];

        aiClarificationEnabled.value = data.type.is_ai_clarification_enabled ?? false;
        aiResolutionEnabled.value = data.type.is_ai_resolution_enabled ?? false;

        submitState.value = useServiceRequestSubmit(data.rawData.store_url_base, data.type.id, data.priority);

        step.value = 'details';
    }

    const hasNextStep = computed(
        () => customFormSteps.value.length > 0 || aiClarificationEnabled.value || aiResolutionEnabled.value,
    );

    function onDetailsNext() {
        if (customFormSteps.value.length > 0) {
            currentCustomStepIndex.value = 0;
            step.value = 'custom-step';
        } else {
            advanceAfterCustomSteps();
        }
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
        advanceAfterCustomSteps();
    }

    function advanceAfterCustomSteps() {
        if (aiClarificationEnabled.value) {
            questionsAndAnswers.value = [];
            generatedQuestions.value = [null, null, null];
            step.value = 'question-1';
        } else if (aiResolutionEnabled.value) {
            step.value = 'ai-resolution';
        } else {
            doSubmit();
        }
    }

    function onQuestionNext(questionNumber) {
        const ref = questionStepRefs.value[questionNumber - 1];
        const answer = ref?.getAnswer();

        if (answer) {
            if (questionsAndAnswers.value.length >= questionNumber) {
                questionsAndAnswers.value[questionNumber - 1] = answer;
            } else {
                questionsAndAnswers.value.push(answer);
            }
        }

        if (questionNumber < 3) {
            step.value = `question-${questionNumber + 1}`;
        } else if (aiResolutionEnabled.value) {
            step.value = 'ai-resolution';
        } else {
            doSubmit();
        }
    }

    function onQuestionBack(questionNumber) {
        if (questionNumber === 1) {
            if (customFormSteps.value.length > 0) {
                currentCustomStepIndex.value = customFormSteps.value.length - 1;
                step.value = 'custom-step';
            } else {
                step.value = 'details';
            }
        } else {
            step.value = `question-${questionNumber - 1}`;
        }
    }

    function onAiResolutionBack() {
        if (aiClarificationEnabled.value) {
            step.value = 'question-3';
        } else if (customFormSteps.value.length > 0) {
            currentCustomStepIndex.value = customFormSteps.value.length - 1;
            step.value = 'custom-step';
        } else {
            step.value = 'details';
        }
    }

    function onAiResolutionResolved(data) {
        aiResolutionResult.value = { attempted: true, successful: true, ...data };
        doSubmit();
    }

    function onAiResolutionDeclined(data) {
        aiResolutionResult.value = { attempted: true, successful: false, ...data };
        doSubmit();
    }

    function onAiResolutionSkip() {
        aiResolutionResult.value = null;
        doSubmit();
    }

    const questionsPayload = computed(() => {
        const payload = {};

        for (const qa of questionsAndAnswers.value) {
            payload[qa.encrypted_question] = qa.answer;
        }

        return payload;
    });

    const formDataForAi = computed(() => {
        if (!submitState.value) return {};

        const allCustomFields = {};
        customStepRefs.value.forEach((stepRef) => {
            if (stepRef?.getFieldValues) {
                Object.assign(allCustomFields, stepRef.getFieldValues());
            }
        });

        return {
            title: submitState.value.title,
            description: submitState.value.description,
            priority_id: detailsData.value?.priority ?? '',
            custom_fields: allCustomFields,
        };
    });

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

        const result = await submitState.value.submitForm(
            allCustomFields,
            questionsAndAnswers.value.length > 0 ? questionsPayload.value : null,
            aiResolutionResult.value,
            () => {
                submittedTitle.value = titleSnapshot;
                wasAiResolved.value = aiResolutionResult.value?.successful === true;
                step.value = 'success';
            },
        );

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

    function setCustomStepRef(index, el) {
        customStepRefs.value[index] = el;
    }

    function setQuestionStepRef(index, el) {
        questionStepRefs.value[index] = el;
    }

    function cacheGeneratedQuestion(questionNumber, field) {
        generatedQuestions.value[questionNumber - 1] = field;
    }

    return {
        step,
        detailsData,
        submittedTitle,
        customFormSteps,
        currentCustomStepIndex,
        submitState,
        isSubmitting,
        submitError,
        fieldErrors,
        aiClarificationEnabled,
        aiResolutionEnabled,
        questionsAndAnswers,
        aiResolutionResult,
        wasAiResolved,
        questionStepRefs,
        generatedQuestions,
        customStepRefs,
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
        doSubmit,
        setCustomStepRef,
        setQuestionStepRef,
        cacheGeneratedQuestion,
    };
}
