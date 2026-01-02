<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    import { ChatBubbleLeftRightIcon, HashtagIcon, UserIcon } from '@heroicons/vue/24/outline';
    import { computed, ref, watch } from 'vue';
    import { useConversations } from '../composables/useConversations';
    import BaseButton from './ui/BaseButton.vue';
    import BaseInput from './ui/BaseInput.vue';
    import BaseModal from './ui/BaseModal.vue';
    import ErrorAlert from './ui/ErrorAlert.vue';
    import UserSearchSelect from './UserSearchSelect.vue';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
        currentUserId: { type: String, required: true },
    });

    const emit = defineEmits(['close', 'created']);

    const { createConversation } = useConversations();

    const conversationType = ref('direct');
    const selectedUserIds = ref([]);
    const channelName = ref('');
    const isPrivate = ref(true);
    const isCreating = ref(false);
    const error = ref('');

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
                error.value = '';
            }
        },
    );

    watch(conversationType, () => {
        selectedUserIds.value = [];
        channelName.value = '';
        error.value = '';
    });

    async function handleCreate() {
        if (!canCreate.value || isCreating.value) return;

        isCreating.value = true;
        error.value = '';

        try {
            const conversation = await createConversation(
                conversationType.value,
                selectedUserIds.value,
                conversationType.value === 'channel' ? channelName.value.trim() : null,
                isPrivate.value,
            );
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
                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        v-model="isPrivate"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 dark:bg-gray-800"
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
