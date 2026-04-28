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
import { computed, ref } from 'vue';
import { clearToken, getAuthHeaders } from '../utils/token.js';

/**
 * @param {string} storeUrlBase  - URL template with `__TYPE__` placeholder
 * @param {string} typeId        - Selected service request type ID
 * @param {string} priorityId    - Selected priority ID (may be empty string)
 */
export function useServiceRequestSubmit(storeUrlBase, typeId, priorityId) {
    const title = ref('');
    const description = ref('');
    const attachments = ref([]);
    const isSubmitting = ref(false);
    const submitError = ref(null);

    const canSubmit = computed(() => title.value.trim() && description.value.trim() && !isSubmitting.value);

    async function submitForm(onSuccess) {
        if (!canSubmit.value) return;

        isSubmitting.value = true;
        submitError.value = null;

        const storeUrl = storeUrlBase.replace('__TYPE__', typeId);

        try {
            await axios.post(
                storeUrl,
                {
                    title: title.value,
                    description: description.value,
                    priority_id: priorityId,
                    attachments: (attachments.value ?? []).map((a) => ({
                        path: a.path,
                        original_file_name: a.originalFileName,
                    })),
                },
                { headers: getAuthHeaders() },
            );

            onSuccess?.();
        } catch (error) {
            if (!error.response || error.response.status === 401) {
                clearToken();
            } else {
                submitError.value = error.response?.data?.errors?.[0] ?? 'Something went wrong. Please try again.';
            }
        } finally {
            isSubmitting.value = false;
        }
    }

    return {
        title,
        description,
        attachments,
        isSubmitting,
        submitError,
        canSubmit,
        submitForm,
    };
}
