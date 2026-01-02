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
    import { UserPlusIcon } from '@heroicons/vue/24/outline';
    import { computed, ref, watch } from 'vue';
    import { useConversations } from '../composables/useConversations';
    import BaseButton from './ui/BaseButton.vue';
    import BaseModal from './ui/BaseModal.vue';
    import ErrorAlert from './ui/ErrorAlert.vue';
    import UserSearchSelect from './UserSearchSelect.vue';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
        conversation: { type: Object, required: true },
    });

    const emit = defineEmits(['close', 'added']);

    const { addParticipant } = useConversations();

    const selectedUserIds = ref([]);
    const isAdding = ref(false);
    const error = ref('');

    const existingParticipantIds = computed(() => {
        return (props.conversation.participants || []).map((participant) => participant.participant_id);
    });

    const canAdd = computed(() => selectedUserIds.value.length > 0);

    watch(
        () => props.isOpen,
        (open) => {
            if (!open) {
                selectedUserIds.value = [];
                error.value = '';
            }
        },
    );

    async function handleAdd() {
        if (!canAdd.value || isAdding.value) return;

        isAdding.value = true;
        error.value = '';

        try {
            for (const userId of selectedUserIds.value) {
                await addParticipant(props.conversation.id, userId);
            }
            emit('added');
        } catch (e) {
            error.value = e.response?.data?.message || 'Failed to add participants';
        } finally {
            isAdding.value = false;
        }
    }

    function handleClose() {
        emit('close');
    }
</script>

<template>
    <BaseModal :is-open="isOpen" title="Add Participants" @close="handleClose">
        <template #icon>
            <UserPlusIcon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
        </template>

        <div>
            <ErrorAlert v-if="error" :message="error" class="mb-4" />

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"> Select Users </label>
                <UserSearchSelect v-model:selected-ids="selectedUserIds" :exclude-ids="existingParticipantIds" />
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Search and select users to add to this channel.
                </p>
            </div>
        </div>

        <template #footer>
            <BaseButton variant="secondary" @click="handleClose">Cancel</BaseButton>
            <BaseButton :disabled="!canAdd" :loading="isAdding" @click="handleAdd">
                {{ isAdding ? 'Adding...' : 'Add Participants' }}
            </BaseButton>
        </template>
    </BaseModal>
</template>
