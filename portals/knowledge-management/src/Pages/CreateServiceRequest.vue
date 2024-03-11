<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
import { defineProps, ref, watch, onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Loading from '@/Components/Loading.vue';
import { Bars3Icon } from '@heroicons/vue/24/outline/index.js';
import { useAuthStore } from '@/Stores/auth.js';
import axios from '@/Globals/Axios.js';
import { useTokenStore } from '@/Stores/token.js';
import wizard from '../../../../widgets/service-request-form/src/FormKit/wizard.js';
import attachRecaptchaScript from '../../../../app-modules/integration-google-recaptcha/resources/js/Services/AttachRecaptchaScript.js';
import getRecaptchaToken from '../../../../app-modules/integration-google-recaptcha/resources/js/Services/GetRecaptchaToken.js';

let { steps, visitedSteps, activeStep, setStep, wizardPlugin } = wizard();

const route = useRoute();

const props = defineProps({
    apiUrl: {
        type: String,
        required: true,
    },
});

const loadingResults = ref(true);
const description = ref('');
const user = ref(null);
const schema = ref([]);
const priorities = ref(null);
const priority = ref(null);
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

        const { getToken } = useTokenStore();
        let token = await getToken();

        // TODO Validate these fields
        data.description = description.value;
        data.priority = priority.value;

        // let recaptchaToken = null;

        // if (formRecaptchaEnabled.value === true) {
        //     recaptchaToken = await getRecaptchaToken(formRecaptchaKey.value);
        // }

        // if (recaptchaToken !== null) {
        //     data['recaptcha-token'] = recaptchaToken;
        // }

        axios
            .post(props.apiUrl + '/service-request/create/' + route.params.typeId, data, {
                headers: { Authorization: `Bearer ${token}` },
            })
            .then((response) => {
                submittedSuccess.value = true;
            })
            .catch((error) => {
                node.setErrors([error]);
            });
    },
});

async function getData() {
    loadingResults.value = true;

    const { getUser } = useAuthStore();

    await getUser().then((authUser) => {
        user.value = authUser;
    });

    const { getToken } = useTokenStore();
    let token = await getToken();

    axios
        .get(props.apiUrl + '/service-request/create/' + route.params.typeId, {
            headers: { Authorization: `Bearer ${token}` },
        })
        .then((response) => {
            loadingResults.value = false;

            schema.value = response.data.schema;
            priorities.value = response.data.priorities;
        });
}
</script>

<template>
    <div>
        <div v-if="loadingResults">
            <Loading />
        </div>
        <div v-else>
            <Breadcrumbs
                currentCrumb="Submit Form"
                :breadcrumbs="[
                    { name: 'Help Center', route: 'home' },
                    { name: 'New Request', route: 'create-service-request' },
                ]"
            ></Breadcrumbs>

            <div
                class="sticky top-0 z-40 flex flex-col items-center border-b border-gray-100 bg-white px-4 py-4 shadow-sm sm:px-6 lg:px-8"
            >
                <button class="w-full p-2.5 lg:hidden" type="button" v-on:click="showMobileMenu = !showMobileMenu">
                    <span class="sr-only">Open sidebar</span>
                    <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
                </button>

                <div class="flex h-full w-full flex-col rounded bg-primary-700 px-12 py-4">
                    <div class="text-right" v-if="submittedSuccess">
                        <button class="p-2 font-bold rounded bg-white text-primary-700 dark:text-primary-400">
                            <router-link :to="{ name: 'create-service-request' }"> Submit Another Request </router-link>
                        </button>
                    </div>
                    <div class="flex flex-col text-left">
                        <h3 class="text-3xl text-white">Help Center</h3>
                        <p class="text-white">Welcome {{ user.first_name }}!</p>
                        <p class="text-white">Please fill out the following information to submit your request.</p>
                    </div>
                </div>
            </div>

            <main class="grid py-10 gap-4" v-if="submittedSuccess">Thank you for submitting a new request.</main>
            <main class="grid py-10 gap-4" v-else>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <select
                    id="priority"
                    v-model="priority"
                    class="w-full rounded border-gray-300 shadow focus:border-primary-600 focus:ring focus:ring-primary-400 focus:ring-opacity-50"
                    required
                >
                    <option default disabled value="Select a priority">Select a priority</option>
                    <option :value="p.id" v-for="p in priorities" :key="p.id">{{ p.name }}</option>
                </select>

                <label for="description" class="block text-sm font-medium text-gray-700">Describe your issue</label>
                <textarea
                    id="description"
                    class="w-full rounded border-gray-300 shadow focus:border-primary-600 focus:ring focus:ring-primary-400 focus:ring-opacity-50"
                    rows="6"
                    placeholder="Please describe your issue here"
                    v-model="description"
                    required
                ></textarea>

                <h3 class="text-xl">Additional Form Information</h3>
                <FormKitSchema :schema="schema" :data="data" />
            </main>
        </div>
    </div>
</template>
