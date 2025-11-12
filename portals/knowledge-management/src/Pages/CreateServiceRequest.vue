<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    import { createMessage, getNode } from '@formkit/core';
    import { defineProps, nextTick, onMounted, reactive, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import wizard from '../../../../widgets/service-request-form/src/FormKit/wizard.js';
    import AppLoading from '../Components/AppLoading.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import Page from '../Components/Page.vue';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';

    let { steps, visitedSteps, activeStep, setStep, wizardPlugin } = wizard();

    const route = useRoute();

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
    });

    const loadingResults = ref(true);
    const user = ref(null);
    const schema = ref([]);
    const submittedSuccess = ref(false);
    const hasGeneratedQuestions = ref(false);
    const isGeneratingQuestions = ref(false);

    watch(
        route,
        function (newRouteValue) {
            getData();
        },
        {
            immediate: true,
        },
    );

    watch(activeStep, async function (newStep) {
        if (!newStep || isGeneratingQuestions.value) {
            return;
        }

        await checkAndGenerateQuestions(newStep);
    });

    onMounted(function () {
        getData();
    });

    const data = reactive({
        steps,
        visitedSteps,
        activeStep,
        plugins: [wizardPlugin],
        setStep: (target) => () => {
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
        setActiveStep: (stepName) => () => {
            const stepNames = Object.keys(steps);
            const targetIndex = stepNames.indexOf(stepName);

            for (let i = 0; i < targetIndex; i++) {
                const stepToValidate = stepNames[i];
                const stepNode = getNode(stepToValidate);

                if (stepNode) {
                    stepNode.walk((node) => {
                        node.store.set(
                            createMessage({
                                key: 'submitted',
                                value: true,
                                visible: false,
                            }),
                        );
                    });
                }
            }

            nextTick(() => {
                for (let i = 0; i < targetIndex; i++) {
                    const step = stepNames[i];

                    if (steps[step].errorCount > 0 || steps[step].blockingCount > 0) {
                        return;
                    }
                }

                data.activeStep = stepName;
            });
        },
        showStepErrors: (stepName) => {
            return (
                (steps[stepName].errorCount > 0 || steps[stepName].blockingCount > 0) &&
                visitedSteps.value &&
                visitedSteps.value.includes(stepName)
            );
        },
        stepIsValid: (stepName) => {
            return steps[stepName].valid && steps[stepName].errorCount === 0;
        },
        stringify: (value) => JSON.stringify(value, null, 2),
        submitForm: async (data, node) => {
            node.clearErrors();

            const { post } = consumer();

            post(props.apiUrl + '/service-request/create/' + route.params.typeId, data)
                .then((response) => {
                    submittedSuccess.value = true;
                })
                .catch((error) => {
                    node.setErrors(error.response.data.errors);
                });
        },
    });

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
                Array.isArray(section.children) ? section.children[0] : Object.values(section.children)[0],
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

            try {
                const { post } = consumer();
                const formNode = getNode('form');
                const formData = formNode ? formNode.value : {};

                const response = await post(
                    props.apiUrl + '/service-request/create/' + route.params.typeId + '/generate-questions',
                    { step: stepName, formData },
                );

                if (response.data.fields && response.data.fields.length > 0) {
                    stepSchema.children = response.data.fields;
                    hasGeneratedQuestions.value = true;
                }
            } finally {
                isGeneratingQuestions.value = false;
            }
        }
    }

    async function getData() {
        loadingResults.value = true;

        const { getUser } = useAuthStore();

        await getUser().then((authUser) => {
            user.value = authUser;
        });

        const { get } = consumer();

        get(props.apiUrl + '/service-request/create/' + route.params.typeId).then((response) => {
            loadingResults.value = false;

            schema.value = response.data.schema;
        });
    }
</script>

<template>
    <div>
        <div v-if="loadingResults">
            <AppLoading />
        </div>
        <div v-else>
            <Page :has-new-request-button="false">
                <template #heading> Help Center </template>

                <template #description>
                    <p>Welcome {{ user.first_name }}!</p>
                    <p>Please fill out the following information to submit your request.</p>
                </template>

                <template #breadcrumbs>
                    <Breadcrumbs
                        currentCrumb="Submit Form"
                        :breadcrumbs="[
                            { name: 'Help Center', route: 'home' },
                            { name: 'New Request', route: 'create-service-request' },
                        ]"
                    />
                </template>

                <main class="grid gap-4" v-if="submittedSuccess">
                    Thank you. Your request has been submitted.

                    <button class="p-2 font-bold rounded bg-white text-brand-700">
                        <router-link :to="{ name: 'create-service-request' }"> Submit Another Request </router-link>
                    </button>
                </main>

                <main class="grid gap-4" v-else>
                    <div v-if="isGeneratingQuestions" class="flex items-center justify-center">
                        <div
                            role="status"
                            aria-live="polite"
                            class="pointer-events-auto flex items-center gap-3 px-4 py-2"
                        >
                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                class="animate-spin h-4 w-4 text-brand-600"
                            >
                                <path
                                    clip-rule="evenodd"
                                    d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                    fill-rule="evenodd"
                                    fill="currentColor"
                                    opacity="0.2"
                                ></path>
                                <path
                                    d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
                                    fill="currentColor"
                                ></path>
                            </svg>

                            <span class="text-sm text-gray-700">Generating questions…</span>
                        </div>
                    </div>
                    <FormKitSchema :schema="schema" :data="data" v-show="!isGeneratingQuestions" />
                </main>
            </Page>
        </div>
    </div>
</template>
