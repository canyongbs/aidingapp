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
*/
import axios from 'axios';
import { ref } from 'vue';

export function useWidgetSignIn(authenticateRequestUrl, { onAuthenticated } = {}) {
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
            const response = await axios.post(authenticateRequestUrl, { email: email.value });
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

            onAuthenticated?.(response.data.token);
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

    function resetToEmail() {
        step.value = 'email';
        errorMessage.value = null;
    }

    return {
        step,
        email,
        code,
        isSubmitting,
        errorMessage,
        submitEmail,
        submitCode,
        resetToEmail,
    };
}
