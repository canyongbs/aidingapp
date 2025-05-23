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
    import { defineProps, onMounted, reactive, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import wizard from '../../../../widgets/service-request-form/src/FormKit/wizard.js';
    import AppLoading from '../Components/AppLoading.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
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

    watch(
        route,
        function (newRouteValue) {
            getData();
        },
        {
            immediate: true,
        },
    );

    onMounted(function () {
        getData();
    });

    const data = reactive({
        steps,
        visitedSteps,
        activeStep,
        plugins: [wizardPlugin],
        setStep: (target) => () => {
            setStep(target);
        },
        setActiveStep: (stepName) => () => {
            data.activeStep = stepName;
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

            // let recaptchaToken = null;

            // if (formRecaptchaEnabled.value === true) {
            //     recaptchaToken = await getRecaptchaToken(formRecaptchaKey.value);
            // }

            // if (recaptchaToken !== null) {
            //     data['recaptcha-token'] = recaptchaToken;
            // }

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
            <div class="sticky top-0 z-40 flex flex-col items-center bg-gray-50">
                <div class="bg-gradient-to-br from-brand-500 to-brand-800 w-full px-6">
                    <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                        <div class="text-right" v-if="submittedSuccess">
                            <button class="p-2 font-bold rounded bg-white text-brand-700 dark:text-brand-400">
                                <router-link :to="{ name: 'create-service-request' }">
                                    Submit Another Request
                                </router-link>
                            </button>
                        </div>
                        <div class="flex flex-col text-left">
                            <h3 class="text-3xl text-white">Help Center</h3>
                            <p class="text-white">Welcome {{ user.first_name }}!</p>
                            <p class="text-white">Please fill out the following information to submit your request.</p>
                        </div>
                    </div>
                </div>
            </div>

            <Breadcrumbs
                class="px-6 py-8"
                currentCrumb="Submit Form"
                :breadcrumbs="[
                    { name: 'Help Center', route: 'home' },
                    { name: 'New Request', route: 'create-service-request' },
                ]"
            >
            </Breadcrumbs>

            <main class="grid px-6 gap-4" v-if="submittedSuccess">Thank you. Your request has been submitted.</main>
            <main class="grid px-6 gap-4" v-else>
                <FormKitSchema :schema="schema" :data="data" />
            </main>
        </div>
    </div>
</template>
