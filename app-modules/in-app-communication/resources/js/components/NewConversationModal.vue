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
    import { ChatBubbleLeftRightIcon, HashtagIcon, UserIcon } from '@heroicons/vue/24/outline';
    import { computed, ref, watch } from 'vue';
    import { useConversations } from '../composables/useConversations';
    import BaseButton from './ui/BaseButton.vue';
    import BaseInput from './ui/BaseInput.vue';
    import BaseModal from './ui/BaseModal.vue';
    import BaseToggle from './ui/BaseToggle.vue';
    import ErrorAlert from './ui/ErrorAlert.vue';
    import UserSearchSelect from './UserSearchSelect.vue';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
        currentUserId: { type: String, required: true },
        confidentialChannelsEnabled: { type: Boolean, default: false },
    });

    const emit = defineEmits(['close', 'created']);

    const { createConversation } = useConversations();

    const DEFAULT_EPHEMERAL_PERIOD = '24_hours';

    const EPHEMERAL_PERIOD_OPTIONS = [
        { value: '', label: 'None' },
        { value: '1_minute', label: '1 Minute' },
        { value: '5_minutes', label: '5 Minutes' },
        { value: '15_minutes', label: '15 Minutes' },
        { value: '1_hour', label: '1 Hour' },
        { value: '24_hours', label: '24 Hours' },
        { value: '7_days', label: '7 Days' },
        { value: '14_days', label: '14 Days' },
        { value: '1_month', label: '1 Month' },
        { value: '3_months', label: '3 Months' },
        { value: '6_months', label: '6 Months' },
        { value: '1_year', label: '1 Year' },
    ];

    const conversationType = ref('direct');
    const selectedUserIds = ref([]);
    const channelName = ref('');
    const isPrivate = ref(true);
    const isConfidential = ref(false);
    const ephemeralPeriod = ref(DEFAULT_EPHEMERAL_PERIOD);
    const isCreating = ref(false);
    const error = ref('');

    const showConfidentialFields = computed(
        () => props.confidentialChannelsEnabled && conversationType.value === 'channel',
    );

    const canCreate = computed(() => {
        if (conversationType.value === 'direct') {
            return selectedUserIds.value.length === 1;
        }
        if (channelName.value.trim().length === 0) {
            return false;
        }
        if (isPrivate.value && selectedUserIds.value.length === 0) {
            return false;
        }
        return true;
    });

    watch(
        () => props.isOpen,
        (open) => {
            if (!open) {
                conversationType.value = 'direct';
                selectedUserIds.value = [];
                channelName.value = '';
                isPrivate.value = true;
                isConfidential.value = false;
                ephemeralPeriod.value = DEFAULT_EPHEMERAL_PERIOD;
                error.value = '';
            }
        },
    );

    watch(conversationType, () => {
        selectedUserIds.value = [];
        channelName.value = '';
        isConfidential.value = false;
        ephemeralPeriod.value = DEFAULT_EPHEMERAL_PERIOD;
        error.value = '';
    });

    // A confidential channel is always invite only, so the privacy choice is made for the user.
    watch(isConfidential, (confidential) => {
        if (confidential) {
            isPrivate.value = true;
        }
    });

    async function handleCreate() {
        if (!canCreate.value || isCreating.value) return;

        isCreating.value = true;
        error.value = '';

        try {
            const conversation = await createConversation({
                type: conversationType.value,
                participantIds: selectedUserIds.value,
                name: conversationType.value === 'channel' ? channelName.value.trim() : null,
                isPrivate: isPrivate.value,
                isConfidential: showConfidentialFields.value && isConfidential.value,
                ephemeralPeriod: ephemeralPeriod.value || null,
            });
            emit('created', conversation);
        } catch (e) {
            error.value = e.response?.data?.message || 'Failed to create conversation';
        } finally {
            isCreating.value = false;
        }
    }

    function handleClose() {
        emit('close');
    }
</script>

<template>
    <BaseModal :is-open="isOpen" title="New Conversation" @close="handleClose">
        <template #icon>
            <ChatBubbleLeftRightIcon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
        </template>

        <div>
            <ErrorAlert v-if="error" :message="error" class="mb-4" />

            <!-- Conversation Type -->
            <div class="mb-5">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="flex-1 flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-all duration-150"
                        :class="[
                            conversationType === 'direct'
                                ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400'
                                : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800',
                        ]"
                        @click="conversationType = 'direct'"
                    >
                        <UserIcon class="w-4 h-4" />
                        Direct Message
                    </button>
                    <button
                        type="button"
                        class="flex-1 flex items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-all duration-150"
                        :class="[
                            conversationType === 'channel'
                                ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400'
                                : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800',
                        ]"
                        @click="conversationType = 'channel'"
                    >
                        <HashtagIcon class="w-4 h-4" />
                        Channel
                    </button>
                </div>
            </div>

            <!-- Confidential -->
            <div v-if="showConfidentialFields" class="mb-5">
                <BaseToggle v-model="isConfidential" label="Confidential" />
            </div>

            <!-- Ephemeral Period -->
            <div v-if="showConfidentialFields && isConfidential" class="mb-5">
                <label
                    for="new-conversation-ephemeral-period"
                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Ephemeral Period
                </label>
                <select
                    id="new-conversation-ephemeral-period"
                    v-model="ephemeralPeriod"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option v-for="option in EPHEMERAL_PERIOD_OPTIONS" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Messages are permanently deleted once they reach this age.
                </p>
            </div>

            <!-- Channel Name -->
            <div v-if="conversationType === 'channel'" class="mb-5">
                <BaseInput v-model="channelName" label="Channel Name" placeholder="Enter channel name" />
            </div>

            <!-- User Selection -->
            <div class="mb-5">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ conversationType === 'direct' ? 'Select User' : 'Add Participants' }}
                </label>
                <UserSearchSelect
                    v-model:selected-ids="selectedUserIds"
                    :exclude-ids="[currentUserId]"
                    :max-selections="conversationType === 'direct' ? 1 : undefined"
                />
            </div>

            <!-- Privacy Toggle -->
            <div v-if="conversationType === 'channel'" class="mb-2">
                <label
                    class="flex items-center gap-3"
                    :class="[isConfidential ? 'cursor-not-allowed opacity-60' : 'cursor-pointer']"
                >
                    <input
                        v-model="isPrivate"
                        type="checkbox"
                        :disabled="isConfidential"
                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 disabled:cursor-not-allowed dark:bg-gray-800"
                    />
                    <span class="text-sm text-gray-700 dark:text-gray-300"> Private channel (invite only) </span>
                </label>
            </div>
        </div>

        <template #footer>
            <BaseButton variant="secondary" @click="handleClose">Cancel</BaseButton>
            <BaseButton :disabled="!canCreate" :loading="isCreating" @click="handleCreate">
                {{ isCreating ? 'Creating...' : 'Create' }}
            </BaseButton>
        </template>
    </BaseModal>
</template>
