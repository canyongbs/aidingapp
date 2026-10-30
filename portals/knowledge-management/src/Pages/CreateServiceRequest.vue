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
    import BaseButton from '@common/BaseButton.vue';
    import LoadingSpinner from '@common/LoadingSpinner.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import Page from '@common/portal/Page.vue';
    import PageCard from '@common/portal/PageCard.vue';
    import { createMessage, getNode } from '@formkit/core';
    import { FormKitSchema } from '@formkit/vue';
    import { UserIcon } from '@heroicons/vue/24/outline';
    import DOMPurify from 'dompurify';
    import { marked } from 'marked';
    import { storeToRefs } from 'pinia';
    import { computed, nextTick, provide, reactive, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import { usePortalAuth } from '../Composables/usePortalAuth.js';
    import { createPasswordStorageCoordinator, passwordStorageKey } from '../FormKit/passwordStorage.js';
    import wizard from '../FormKit/wizard.js';
    import { apiPost } from '../Services/api.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useCreateServiceRequestData } from './loaders.js';

    marked.setOptions({
        breaks: true,
        gfm: true,
    });

    function renderMarkdown(content) {
        if (!content) return '';
        try {
            const html = marked.parse(content, { async: false });
            return DOMPurify.sanitize(html);
        } catch (error) {
            return DOMPurify.sanitize(content);
        }
    }

    let { steps, visitedSteps, activeStep, currentStep, totalSteps, setStep, wizardPlugin } = wizard();

    const percentComplete = computed(() =>
        totalSteps.value > 0 ? Math.round((currentStep.value / totalSteps.value) * 100) : 0,
    );

    const route = useRoute();

    // The authenticated contact is resolved during portal boot, so it is available
    // synchronously here.
    const { user } = storeToRefs(useAuthStore());

    const { logout } = usePortalAuth();

    // The form schema for the selected type arrives via the route data loader.
    const { data: initialData } = useCreateServiceRequestData();

    const schema = ref([]);
    const submittedSuccess = ref(false);
    const wasAiResolved = ref(false);
    const hasGeneratedQuestions = ref(false);
    const isGeneratingQuestions = ref(false);
    const categoryData = ref(null);
    const numberOfClarifyingQuestions = ref(1);
    const isEvaluatingAiResolution = ref(false);
    const aiResolutionData = ref(null);
    const aiResolutionAttempted = ref(null);
    const showAiResolutionStep = ref(false);
    const preloadedResolutionPromise = ref(null);
    const passwordStorage = createPasswordStorageCoordinator();

    provide(passwordStorageKey, passwordStorage);

    watch(
        initialData,
        (response) => {
            if (!response) {
                return;
            }

            schema.value = response.schema;

            categoryData.value = response.category || null;
            numberOfClarifyingQuestions.value = response.number_of_clarifying_questions ?? 1;
        },
        { immediate: true },
    );

    watch(activeStep, async function (newStep) {
        if (!newStep || isGeneratingQuestions.value) {
            return;
        }

        await checkAndGenerateQuestions(newStep);
    });

    const categoryBreadcrumbs = computed(() => {
        if (!categoryData.value) return [];

        const breadcrumbs = [];

        // Add ancestors first (root to parent)
        if (categoryData.value.ancestors) {
            categoryData.value.ancestors.forEach((ancestor) => {
                breadcrumbs.push({
                    name: ancestor.name,
                    route: 'create-service-request',
                    params: { categoryId: ancestor.id },
                });
            });
        }

        // Add the category itself
        breadcrumbs.push({
            name: categoryData.value.name,
            route: 'create-service-request',
            params: { categoryId: categoryData.value.id },
        });

        return breadcrumbs;
    });

    const data = reactive({
        steps,
        visitedSteps,
        activeStep,
        currentStep,
        totalSteps,
        percentComplete,
        plugins: [wizardPlugin],
        preparePasswordSubmit: passwordStorage.prepareSubmit,
        setStep: (target) => async () => {
            if (!(await passwordStorage.flushAll())) {
                return;
            }

            const currentStepNode = getNode(activeStep.value);

            if (currentStepNode) {
                currentStepNode.walk((node) => {
                    node.store.set(
                        createMessage({
                            key: 'submitted',
                            value: true,
                            visible: false,
                        }),
                    );
                });
            }

            if (target === 1) {
                nextTick(() => {
                    if (steps[activeStep.value].errorCount === 0 && steps[activeStep.value].blockingCount === 0) {
                        setStep(target);
                    }
                });

                return;
            }

            setStep(target);
        },
        isLastStep: () => {
            const stepNames = Object.keys(steps);
            return activeStep.value === stepNames[stepNames.length - 1];
        },
        stringify: (value) => JSON.stringify(value, null, 2),
        submitForm: async (formData, node) => {
            node.clearErrors();

            // If not on the last step, move to next step instead of submitting
            if (!data.isLastStep()) {
                data.setStep(1)();
                return;
            }

            if (!showAiResolutionStep.value && !aiResolutionData.value) {
                const shouldShowAiResolution = await evaluateAiResolution();
                if (shouldShowAiResolution) {
                    return;
                }
            }

            const submitData = { ...formData };

            if (aiResolutionAttempted.value) {
                submitData.is_ai_resolution_attempted = true;
                submitData.ai_resolution_confidence_score = aiResolutionAttempted.value.confidenceScore;
            }

            apiPost('/service-request/create/' + route.params.typeId, submitData)
                .then((response) => {
                    submittedSuccess.value = true;
                })
                .catch((error) => {
                    node.setErrors(error.response.data.errors);
                });
        },
    });

    async function preloadAiResolution() {
        try {
            if (!(await passwordStorage.flushAll())) {
                return null;
            }

            const formNode = getNode('form');
            const formData = formNode ? formNode.value : {};

            const response = await apiPost(
                '/service-request/create/' + route.params.typeId + '/evaluate-ai-resolution',
                { formData },
            );

            return response;
        } catch {
            return null;
        }
    }

    async function evaluateAiResolution() {
        isEvaluatingAiResolution.value = true;

        try {
            if (!(await passwordStorage.flushAll())) {
                return false;
            }

            let response = null;

            if (preloadedResolutionPromise.value) {
                response = await preloadedResolutionPromise.value;
                preloadedResolutionPromise.value = null;
            }

            if (!response) {
                const formNode = getNode('form');
                const formData = formNode ? formNode.value : {};

                response = await apiPost('/service-request/create/' + route.params.typeId + '/evaluate-ai-resolution', {
                    formData,
                });
            }

            if (response.confidence_score !== undefined) {
                aiResolutionAttempted.value = {
                    confidenceScore: response.confidence_score,
                };
            }

            if (response.is_ai_resolution_available) {
                aiResolutionData.value = {
                    confidenceScore: response.confidence_score,
                    proposedAnswer: response.proposed_answer,
                    encryptedProposedAnswer: response.encrypted_proposed_answer,
                };
                showAiResolutionStep.value = true;
                return true;
            }

            return false;
        } catch (error) {
            console.error('Error evaluating AI resolution:', error);
            return false;
        } finally {
            isEvaluatingAiResolution.value = false;
        }
    }

    async function handleAiResolutionAccepted() {
        if (!(await passwordStorage.flushAll())) {
            return;
        }

        const formNode = getNode('form');
        const formData = formNode ? formNode.value : {};

        try {
            await apiPost('/service-request/create/' + route.params.typeId, {
                ...formData,
                is_ai_resolution_attempted: true,
                is_ai_resolution_successful: true,
                ai_resolution_confidence_score: aiResolutionData.value?.confidenceScore,
                encrypted_ai_proposed_answer: aiResolutionData.value?.encryptedProposedAnswer,
            });

            wasAiResolved.value = true;
            submittedSuccess.value = true;
        } catch (error) {
            console.error('Error submitting with AI resolution:', error);
        }
    }

    async function handleAiResolutionDeclined() {
        showAiResolutionStep.value = false;

        if (!(await passwordStorage.flushAll())) {
            return;
        }

        const formNode = getNode('form');
        const formData = formNode ? formNode.value : {};

        try {
            await apiPost('/service-request/create/' + route.params.typeId, {
                ...formData,
                is_ai_resolution_attempted: true,
                is_ai_resolution_successful: false,
                ai_resolution_confidence_score: aiResolutionData.value?.confidenceScore,
                encrypted_ai_proposed_answer: aiResolutionData.value?.encryptedProposedAnswer,
            });

            submittedSuccess.value = true;
        } catch (error) {
            console.error('Error submitting after declining AI resolution:', error);
        }
    }

    async function checkAndGenerateQuestions(stepName) {
        if (hasGeneratedQuestions.value) {
            return;
        }

        if (stepName === 'Main') {
            return;
        }

        const formKitSchema = schema.value;

        if (!formKitSchema || !formKitSchema.children) {
            return;
        }

        const rootChildren = Array.isArray(formKitSchema.children)
            ? formKitSchema.children
            : Object.values(formKitSchema.children);

        const formBody = rootChildren.find(
            (child) =>
                child &&
                child.$el === 'div' &&
                ((child.attrs && child.attrs.class === 'form-body') ||
                    (child.attrs &&
                        child.attrs.class &&
                        child.attrs.class.includes &&
                        child.attrs.class.includes('form-body'))),
        );

        if (!formBody || !formBody.children) {
            return;
        }

        const sections = Array.isArray(formBody.children) ? formBody.children : Object.values(formBody.children);

        const groups = sections
            .filter((sectionNode) => sectionNode && sectionNode.$el === 'section')
            .map((section) =>
                (Array.isArray(section.children) ? section.children : Object.values(section.children)).find(
                    (child) => child && child.$formkit === 'group',
                ),
            )
            .filter(Boolean);

        const stepNames = groups.map((group) => group.name || group.id).filter(Boolean);
        const lastStepName = stepNames[stepNames.length - 1];
        if (stepName !== lastStepName) {
            return;
        }

        const stepSchema = groups.find((group) => (group.name || group.id) === stepName);

        if (!stepSchema) {
            return;
        }

        const hasFields = stepSchema.children && stepSchema.children.length > 0;

        if (!hasFields) {
            isGeneratingQuestions.value = true;

            if (!(await passwordStorage.flushAll())) {
                isGeneratingQuestions.value = false;
                return;
            }

            preloadedResolutionPromise.value = preloadAiResolution();

            try {
                const formNode = getNode('form');
                const formData = formNode ? formNode.value : {};

                const response = await apiPost(
                    '/service-request/create/' + route.params.typeId + '/generate-questions',
                    { step: stepName, formData },
                );

                if (response.fields && response.fields.length > 0) {
                    stepSchema.children = response.fields;
                    hasGeneratedQuestions.value = true;
                }
            } finally {
                isGeneratingQuestions.value = false;
            }
        }
    }
</script>

<template>
    <Page :has-new-request-button="false">
        <template #heading> Help Center </template>

        <template #description>
            <p>Welcome {{ user?.first_name }}!</p>
            <p>Please fill out the following information to submit your request.</p>
        </template>

        <template #breadcrumbs>
            <Breadcrumbs
                currentCrumb="Submit Form"
                :breadcrumbs="[
                    { name: 'Help Center', route: 'home' },
                    { name: 'New Request', route: 'create-service-request' },
                    ...categoryBreadcrumbs,
                ]"
            />
        </template>

        <PageCard>
            <div class="grid gap-4" v-if="submittedSuccess">
                <template v-if="wasAiResolved">
                    <p>Great! We're glad the AI was able to resolve your issue.</p>
                    <p class="text-sm text-gray-600">
                        A record of this interaction has been saved. If you need further assistance, feel free to submit
                        a new request.
                    </p>
                </template>
                <template v-else>
                    <p>Thank you. Your request has been submitted.</p>
                </template>

                <BaseButton tag="router-link" :to="{ name: 'create-service-request' }" color="gray" size="md">
                    Submit Another Request
                </BaseButton>
            </div>

            <div class="grid gap-4" v-else>
                <div v-if="isGeneratingQuestions || isEvaluatingAiResolution" class="flex items-center justify-center">
                    <div role="status" aria-live="polite" class="pointer-events-auto flex items-center gap-3 px-4 py-2">
                        <LoadingSpinner size="sm" class="text-brand-600" />

                        <span v-if="isGeneratingQuestions" class="text-sm text-gray-700"
                            >Generating {{ numberOfClarifyingQuestions === 1 ? 'question' : 'questions' }}…</span
                        >
                        <span v-else class="text-sm text-gray-700">Evaluating your request…</span>
                    </div>
                </div>

                <div v-if="showAiResolutionStep" class="bg-white rounded-lg p-6 shadow-xs">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">AI Resolution Available</h3>

                    <p class="text-gray-600 mb-4">
                        Our AI has considered your request and may be able to answer it immediately. Please review the
                        potential answer below:
                    </p>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div
                            class="prose prose-sm max-w-none text-gray-800"
                            v-html="renderMarkdown(aiResolutionData?.proposedAnswer)"
                        ></div>
                    </div>

                    <p class="text-gray-600 mb-6">
                        Please tell us if this resolved your issue. If not, we will escalate this ticket to a support
                        team member.
                    </p>

                    <div class="flex gap-3">
                        <BaseButton color="success" size="md" @click="handleAiResolutionAccepted">
                            Yes (Resolved)
                        </BaseButton>
                        <BaseButton color="gray" size="md" @click="handleAiResolutionDeclined"> No </BaseButton>
                    </div>
                </div>

                <div
                    class="prose max-w-none"
                    v-show="!isGeneratingQuestions && !isEvaluatingAiResolution && !showAiResolutionStep"
                >
                    <div
                        v-if="user?.email"
                        class="not-prose mb-6 flex items-center justify-between gap-4 rounded-lg border border-brand-100 bg-brand-50 px-4 py-3"
                    >
                        <div class="flex min-w-0 items-center gap-3">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-200 text-gray-700"
                            >
                                <UserIcon class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 text-sm leading-tight">
                                <p class="text-xs text-gray-500">Signed in as</p>
                                <p class="truncate font-semibold text-gray-800">{{ user.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="h-6 w-px bg-gray-300"></span>
                            <button
                                type="button"
                                @click="logout"
                                class="cursor-pointer text-sm font-bold text-brand-600 transition-colors hover:text-brand-800"
                            >
                                Sign out
                            </button>
                        </div>
                    </div>

                    <div v-if="totalSteps > 1" class="not-prose">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900"
                                >Step {{ currentStep }} of {{ totalSteps }}</span
                            >
                            <span class="text-sm text-gray-500">{{ percentComplete }}% complete</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-200">
                            <div
                                class="h-2 rounded-full bg-brand-600 transition-all"
                                :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
                            ></div>
                        </div>
                    </div>
                    <hr v-if="totalSteps > 1" class="not-prose my-6 border-0 border-t border-gray-200" />

                    <FormKitSchema :schema="schema" :data="data" />
                </div>
            </div>
        </PageCard>
    </Page>
</template>
