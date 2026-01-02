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
    import { ArrowLeftIcon, ShieldCheckIcon, UserGroupIcon, UserPlusIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { computed, ref } from 'vue';
    import { useConversations } from '../composables/useConversations';
    import AddParticipantsModal from './AddParticipantsModal.vue';
    import Avatar from './ui/Avatar.vue';
    import BaseButton from './ui/BaseButton.vue';
    import ConfirmModal from './ui/ConfirmModal.vue';
    import ErrorAlert from './ui/ErrorAlert.vue';

    const props = defineProps({
        conversation: { type: Object, required: true },
        currentUserId: { type: String, required: true },
        showBackButton: { type: Boolean, default: false },
    });

    const emit = defineEmits(['back', 'participants-updated']);

    const { leaveConversation, removeParticipant, updateParticipant } = useConversations();

    const isLeaving = ref(false);
    const removingUserId = ref(null);
    const updatingManagerUserId = ref(null);
    const error = ref('');
    const showAddParticipantsModal = ref(false);

    // Confirmation modal state
    const confirmModal = ref({
        isOpen: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        variant: 'primary',
        action: null,
    });

    function showConfirm({ title, message, confirmText = 'Confirm', variant = 'primary', action }) {
        confirmModal.value = { isOpen: true, title, message, confirmText, variant, action };
    }

    function closeConfirm() {
        confirmModal.value.isOpen = false;
    }

    async function handleConfirm() {
        if (confirmModal.value.action) {
            await confirmModal.value.action();
        }
        closeConfirm();
    }

    const participants = computed(() => props.conversation.participants || []);

    const currentUserParticipant = computed(() =>
        participants.value.find((participant) => participant.participant_id === props.currentUserId),
    );

    const isManager = computed(() => currentUserParticipant.value?.is_manager || false);

    const managerCount = computed(() => participants.value.filter((participant) => participant.is_manager).length);

    const isLastManager = computed(() => isManager.value && managerCount.value === 1);

    const canLeaveAsLastManager = computed(() => {
        return props.conversation.is_private && participants.value.length === 1;
    });

    const canLeave = computed(() => {
        if (isLastManager.value) {
            return canLeaveAsLastManager.value;
        }
        return true;
    });

    function handleLeave() {
        if (isLeaving.value || !canLeave.value) return;

        showConfirm({
            title: 'Leave Channel',
            message:
                'Are you sure you want to leave this channel? You will need to be re-invited to rejoin if it is private.',
            confirmText: 'Leave',
            variant: 'danger',
            action: async () => {
                isLeaving.value = true;
                error.value = '';
                try {
                    await leaveConversation(props.conversation.id);
                } catch (e) {
                    error.value = e.response?.data?.message || 'Failed to leave channel';
                } finally {
                    isLeaving.value = false;
                }
            },
        });
    }

    function handleRemoveParticipant(userId, userName) {
        if (removingUserId.value) return;

        showConfirm({
            title: 'Remove Participant',
            message: `Are you sure you want to remove ${userName || 'this participant'} from the channel?`,
            confirmText: 'Remove',
            variant: 'danger',
            action: async () => {
                removingUserId.value = userId;
                try {
                    await removeParticipant(props.conversation.id, userId);
                    emit('participants-updated');
                } catch (e) {
                    error.value = e.response?.data?.message || 'Failed to remove participant';
                } finally {
                    removingUserId.value = null;
                }
            },
        });
    }

    function handleToggleManager(participant) {
        if (updatingManagerUserId.value) return;

        const newIsManager = !participant.is_manager;
        const name = participant.participant?.name || 'this user';

        showConfirm({
            title: newIsManager ? 'Promote to Manager' : 'Remove Manager Role',
            message: newIsManager
                ? `Are you sure you want to make ${name} a manager? They will be able to manage participants and channel settings.`
                : `Are you sure you want to remove ${name}'s manager role?`,
            confirmText: newIsManager ? 'Promote' : 'Remove Role',
            variant: 'primary',
            action: async () => {
                updatingManagerUserId.value = participant.participant_id;
                error.value = '';
                try {
                    await updateParticipant(props.conversation.id, participant.participant_id, {
                        is_manager: newIsManager,
                    });
                    emit('participants-updated');
                } catch (e) {
                    error.value = e.response?.data?.message || `Failed to update manager role`;
                } finally {
                    updatingManagerUserId.value = null;
                }
            },
        });
    }

    function handleParticipantsAdded() {
        showAddParticipantsModal.value = false;
        emit('participants-updated');
    }
</script>

<template>
    <div class="flex h-full flex-col bg-white dark:bg-gray-900">
        <!-- Header -->
        <div
            class="bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 px-4 py-4 shadow-md shrink-0"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button
                        v-if="showBackButton"
                        type="button"
                        class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                        @click="emit('back')"
                    >
                        <ArrowLeftIcon class="w-5 h-5" />
                    </button>
                    <div class="bg-white/20 p-2 rounded-lg">
                        <UserGroupIcon class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-white tracking-tight">Participants</h3>
                        <p class="text-sm text-white/80">
                            {{ participants.length }} {{ participants.length === 1 ? 'member' : 'members' }}
                        </p>
                    </div>
                </div>
                <button
                    v-if="isManager"
                    type="button"
                    class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                    title="Add participants"
                    @click="showAddParticipantsModal = true"
                >
                    <UserPlusIcon class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Error Message -->
        <ErrorAlert v-if="error" :message="error" class="mx-4 mt-4" />

        <!-- Participant List -->
        <div class="flex-1 overflow-y-auto">
            <div
                v-for="participant in participants"
                :key="participant.id"
                class="flex items-center justify-between px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800"
            >
                <div class="flex items-center gap-3">
                    <Avatar
                        :src="participant.participant?.avatar_url"
                        :name="participant.participant?.name || 'Unknown'"
                        size="sm"
                        ring
                    />

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ participant.participant?.name || 'Unknown User' }}
                            <span
                                v-if="participant.participant_id === currentUserId"
                                class="text-gray-500 dark:text-gray-400"
                            >
                                (you)
                            </span>
                        </p>
                        <p v-if="participant.is_manager" class="text-xs text-primary-600 dark:text-primary-400">
                            Manager
                        </p>
                    </div>
                </div>

                <!-- Action Buttons (for managers, not for self) -->
                <div v-if="isManager && participant.participant_id !== currentUserId" class="flex items-center gap-1">
                    <!-- Toggle Manager Button -->
                    <button
                        type="button"
                        class="rounded-lg p-1.5 transition-colors"
                        :class="[
                            participant.is_manager
                                ? 'text-primary-600 hover:bg-primary-50 hover:text-primary-700 dark:text-primary-400 dark:hover:bg-primary-900/20 dark:hover:text-primary-300'
                                : 'text-gray-400 hover:bg-gray-100 hover:text-primary-600 dark:hover:bg-gray-700 dark:hover:text-primary-400',
                        ]"
                        :disabled="updatingManagerUserId === participant.participant_id"
                        :title="participant.is_manager ? 'Remove manager role' : 'Make manager'"
                        @click="handleToggleManager(participant)"
                    >
                        <ShieldCheckIcon class="h-4 w-4" />
                    </button>

                    <!-- Remove Button -->
                    <button
                        type="button"
                        class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-700 dark:hover:text-red-400 transition-colors"
                        :disabled="removingUserId === participant.participant_id"
                        title="Remove participant"
                        @click="handleRemoveParticipant(participant.participant_id, participant.participant?.name)"
                    >
                        <XMarkIcon class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Leave Button -->
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">
            <BaseButton variant="danger" class="w-full" :loading="isLeaving" :disabled="!canLeave" @click="handleLeave">
                {{ isLeaving ? 'Leaving...' : 'Leave Channel' }}
            </BaseButton>
            <p v-if="!canLeave" class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                You must assign another manager before leaving.
            </p>
        </div>

        <!-- Add Participants Modal -->
        <AddParticipantsModal
            :is-open="showAddParticipantsModal"
            :conversation="conversation"
            @close="showAddParticipantsModal = false"
            @added="handleParticipantsAdded"
        />

        <!-- Confirmation Modal -->
        <ConfirmModal
            :is-open="confirmModal.isOpen"
            :title="confirmModal.title"
            :message="confirmModal.message"
            :confirm-text="confirmModal.confirmText"
            :variant="confirmModal.variant"
            @confirm="handleConfirm"
            @cancel="closeConfirm"
        />
    </div>
</template>
