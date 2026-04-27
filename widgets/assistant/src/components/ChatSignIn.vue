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
    import axios from 'axios';
    import { ref } from 'vue';

    const props = defineProps({
        authenticateRequestUrl: { type: String, required: true },
    });

    const emit = defineEmits(['authenticated', 'cancel']);

    const step = ref('email');
    const email = ref('');
    const code = ref('');
    const authenticationUrl = ref(null);
    const isSubmitting = ref(false);
    const errorMessage = ref(null);

    async function submitEmail() {
        if (!email.value.trim() || isSubmitting.value) return;

        isSubmitting.value = true;
        errorMessage.value = null;

        try {
            const response = await axios.post(props.authenticateRequestUrl, { email: email.value });
            authenticationUrl.value = response.data.authentication_url;
            step.value = 'code';
        } catch (error) {
            if (error.response?.data?.errors?.email) {
                errorMessage.value = error.response.data.errors.email[0];
            } else {
                errorMessage.value = 'Something went wrong. Please try again.';
            }
        } finally {
            isSubmitting.value = false;
        }
    }

    async function submitCode() {
        if (!code.value.trim() || isSubmitting.value) return;

        isSubmitting.value = true;
        errorMessage.value = null;

        try {
            const response = await axios.post(authenticationUrl.value, { code: code.value });

            if (response.data.is_expired) {
                errorMessage.value = 'This code has expired. Please start over.';
                step.value = 'email';
                code.value = '';
                return;
            }

            emit('authenticated', response.data.token);
        } catch (error) {
            if (error.response?.data?.errors?.code) {
                errorMessage.value = error.response.data.errors.code[0];
            } else {
                errorMessage.value = 'Something went wrong. Please try again.';
            }
        } finally {
            isSubmitting.value = false;
        }
    }
</script>

<template>
    <div class="flex-1 flex flex-col items-center justify-center px-8 py-10 gap-6">
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900">Sign In</h3>
            <p v-if="step === 'email'" class="mt-1 text-sm text-gray-500">
                Enter your email to receive a sign-in code.
            </p>
            <p v-else class="mt-1 text-sm text-gray-500">
                Enter the 6-digit code sent to <span class="font-medium text-gray-700">{{ email }}</span
                >.
            </p>
        </div>

        <form v-if="step === 'email'" class="w-full flex flex-col gap-4" @submit.prevent="submitEmail">
            <div class="flex flex-col gap-1.5">
                <label for="widget-signin-email" class="text-sm font-medium text-gray-700">Email address</label>
                <input
                    id="widget-signin-email"
                    v-model="email"
                    type="email"
                    autocomplete="email"
                    required
                    placeholder="you@example.com"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                />
            </div>

            <p v-if="errorMessage" class="text-sm text-red-600">{{ errorMessage }}</p>

            <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full bg-brand-500 hover:bg-brand-600 disabled:opacity-60 text-white font-medium text-sm rounded-lg px-4 py-2.5 transition-colors"
            >
                {{ isSubmitting ? 'Sending...' : 'Send Code' }}
            </button>
        </form>

        <form v-else class="w-full flex flex-col gap-4" @submit.prevent="submitCode">
            <div class="flex flex-col gap-1.5">
                <label for="widget-signin-code" class="text-sm font-medium text-gray-700">Verification code</label>
                <input
                    id="widget-signin-code"
                    v-model="code"
                    type="text"
                    inputmode="numeric"
                    pattern="\d{6}"
                    maxlength="6"
                    autocomplete="one-time-code"
                    required
                    placeholder="123456"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 text-center tracking-widest focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                />
            </div>

            <p v-if="errorMessage" class="text-sm text-red-600">{{ errorMessage }}</p>

            <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full bg-brand-500 hover:bg-brand-600 disabled:opacity-60 text-white font-medium text-sm rounded-lg px-4 py-2.5 transition-colors"
            >
                {{ isSubmitting ? 'Verifying...' : 'Verify Code' }}
            </button>

            <button
                type="button"
                @click="
                    step = 'email';
                    errorMessage = null;
                "
                class="text-sm text-gray-500 hover:text-gray-700 transition-colors"
            >
                Use a different email
            </button>
        </form>

        <button
            type="button"
            @click="$emit('cancel')"
            class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
        >
            Back to Chat
        </button>
    </div>
</template>
