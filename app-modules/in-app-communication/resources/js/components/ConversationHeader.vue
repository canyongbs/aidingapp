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
    import {
        AtSymbolIcon,
        BellIcon,
        BellSlashIcon,
        Cog6ToothIcon,
        GlobeAltIcon,
        HashtagIcon,
        LockClosedIcon,
        PencilIcon,
        UserGroupIcon,
    } from '@heroicons/vue/24/outline';
    import { computed, ref } from 'vue';
    import { useConversationDisplay } from '../composables/useConversationDisplay';
    import BaseButton from './ui/BaseButton.vue';
    import BaseInput from './ui/BaseInput.vue';
    import BaseModal from './ui/BaseModal.vue';
    import ConfirmModal from './ui/ConfirmModal.vue';
    import DropdownMenu from './ui/DropdownMenu.vue';

    const props = defineProps({
        conversation: { type: Object, required: true },
        currentUserId: { type: String, required: true },
    });

    const emit = defineEmits(['show-participants', 'update-settings', 'update-conversation']);

    const isUpdatingPrivacy = ref(false);
    const showPrivacyConfirm = ref(false);
    const showRenameModal = ref(false);
    const newChannelName = ref('');
    const isRenaming = ref(false);

    const isManager = computed(() => {
        const participant = props.conversation.participants?.find((p) => p.participant_id === props.currentUserId);
        return participant?.is_manager || false;
    });

    const notificationOptions = [
        { value: 'all', label: 'All Messages', description: 'Receive notifications for all messages', icon: BellIcon },
        {
            value: 'mentions',
            label: 'Mentions Only',
            description: 'Only receive notifications when mentioned',
            icon: AtSymbolIcon,
        },
        { value: 'none', label: 'Muted', description: 'Do not receive any notifications', icon: BellSlashIcon },
    ];

    const currentNotificationPreference = computed(() => {
        return props.conversation.notification_preference || 'all';
    });

    const currentNotificationIcon = computed(() => {
        const option = notificationOptions.find(
            (notificationOption) => notificationOption.value === currentNotificationPreference.value,
        );
        return option?.icon || BellIcon;
    });

    function selectNotificationPreference(value) {
        emit('update-settings', { notification_preference: value });
        notificationMenuRef.value?.close();
    }

    function requestPrivacyChange() {
        settingsMenuRef.value?.close();
        showPrivacyConfirm.value = true;
    }

    async function confirmPrivacyChange() {
        if (isUpdatingPrivacy.value) return;

        isUpdatingPrivacy.value = true;
        try {
            emit('update-conversation', { is_private: !props.conversation.is_private });
        } finally {
            isUpdatingPrivacy.value = false;
            showPrivacyConfirm.value = false;
        }
    }

    function cancelPrivacyChange() {
        showPrivacyConfirm.value = false;
    }

    function openRenameModal() {
        settingsMenuRef.value?.close();
        newChannelName.value = props.conversation.name || '';
        showRenameModal.value = true;
    }

    async function confirmRename() {
        if (isRenaming.value || !newChannelName.value.trim()) return;

        isRenaming.value = true;
        try {
            emit('update-conversation', { name: newChannelName.value.trim() });
            showRenameModal.value = false;
        } finally {
            isRenaming.value = false;
        }
    }

    function cancelRename() {
        showRenameModal.value = false;
        newChannelName.value = '';
    }

    const notificationMenuRef = ref(null);
    const settingsMenuRef = ref(null);

    const { displayName, subtitle, avatarUrl } = useConversationDisplay(
        () => props.conversation,
        () => props.currentUserId,
    );
</script>

<template>
    <div
        class="flex items-center justify-between bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 px-4 md:px-6 py-4 shadow-md shrink-0"
    >
        <div class="flex items-center gap-3">
            <!-- Prepend slot (for mobile back button) -->
            <slot name="prepend" />

            <!-- Avatar -->
            <template v-if="conversation.type === 'channel'">
                <div class="relative bg-white/20 p-2 rounded-lg">
                    <HashtagIcon class="w-5 h-5 text-white" />
                    <LockClosedIcon
                        v-if="conversation.is_private"
                        class="absolute -bottom-0.5 -right-0.5 h-3 w-3 text-white/80"
                    />
                </div>
            </template>
            <template v-else>
                <img
                    :src="avatarUrl"
                    :alt="displayName"
                    class="h-10 w-10 rounded-full object-cover ring-2 ring-white/30"
                />
            </template>

            <!-- Info -->
            <div>
                <h3 class="font-semibold text-white tracking-tight">{{ displayName }}</h3>
                <p class="text-sm text-white/80">{{ subtitle }}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <!-- Notification Preferences -->
            <DropdownMenu ref="notificationMenuRef" title="Notification Settings">
                <template #trigger="{ toggle }">
                    <button
                        type="button"
                        class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                        :title="`Notifications: ${notificationOptions.find((notificationOption) => notificationOption.value === currentNotificationPreference)?.label}`"
                        @click="toggle"
                    >
                        <component :is="currentNotificationIcon" class="w-5 h-5" />
                    </button>
                </template>

                <button
                    v-for="option in notificationOptions"
                    :key="option.value"
                    type="button"
                    class="w-full flex items-start gap-3 px-3 py-2 rounded-md text-left transition-colors"
                    :class="[
                        currentNotificationPreference === option.value
                            ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
                    ]"
                    @click="selectNotificationPreference(option.value)"
                >
                    <component
                        :is="option.icon"
                        class="w-5 h-5 shrink-0 mt-0.5"
                        :class="[
                            currentNotificationPreference === option.value
                                ? 'text-primary-600 dark:text-primary-400'
                                : 'text-gray-400 dark:text-gray-500',
                        ]"
                    />
                    <div>
                        <p class="text-sm font-medium">{{ option.label }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ option.description }}</p>
                    </div>
                </button>
            </DropdownMenu>

            <button
                v-if="conversation.type === 'channel'"
                type="button"
                class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                title="Show participants"
                @click="emit('show-participants')"
            >
                <UserGroupIcon class="w-5 h-5" />
            </button>

            <!-- Channel Settings (Managers only) -->
            <DropdownMenu
                v-if="conversation.type === 'channel' && isManager"
                ref="settingsMenuRef"
                title="Channel Settings"
            >
                <template #trigger="{ toggle }">
                    <button
                        type="button"
                        class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                        title="Channel settings"
                        @click="toggle"
                    >
                        <Cog6ToothIcon class="w-5 h-5" />
                    </button>
                </template>

                <button
                    type="button"
                    class="w-full flex items-start gap-3 px-3 py-2 rounded-md text-left transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="openRenameModal"
                >
                    <PencilIcon class="w-5 h-5 shrink-0 mt-0.5 text-gray-400 dark:text-gray-500" />
                    <div>
                        <p class="text-sm font-medium">Rename Channel</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Change the channel name</p>
                    </div>
                </button>

                <button
                    type="button"
                    class="w-full flex items-start gap-3 px-3 py-2 rounded-md text-left transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    :disabled="isUpdatingPrivacy"
                    @click="requestPrivacyChange"
                >
                    <component
                        :is="conversation.is_private ? GlobeAltIcon : LockClosedIcon"
                        class="w-5 h-5 shrink-0 mt-0.5 text-gray-400 dark:text-gray-500"
                    />
                    <div>
                        <p class="text-sm font-medium">
                            {{ conversation.is_private ? 'Make Public' : 'Make Private' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{
                                conversation.is_private
                                    ? 'Allow anyone to find and join this channel'
                                    : 'Restrict channel to invited members only'
                            }}
                        </p>
                    </div>
                </button>
            </DropdownMenu>
        </div>
    </div>

    <!-- Privacy Change Confirmation Modal -->
    <ConfirmModal
        :is-open="showPrivacyConfirm"
        :title="conversation.is_private ? 'Make Channel Public' : 'Make Channel Private'"
        :message="
            conversation.is_private
                ? 'Are you sure you want to make this channel public? Anyone will be able to find and join this channel.'
                : 'Are you sure you want to make this channel private? Only invited members will be able to access it.'
        "
        :confirm-text="conversation.is_private ? 'Make Public' : 'Make Private'"
        :loading="isUpdatingPrivacy"
        variant="primary"
        @confirm="confirmPrivacyChange"
        @cancel="cancelPrivacyChange"
    />

    <!-- Rename Channel Modal -->
    <BaseModal :is-open="showRenameModal" title="Rename Channel" @close="cancelRename">
        <template #icon>
            <PencilIcon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
        </template>

        <form @submit.prevent="confirmRename">
            <BaseInput
                v-model="newChannelName"
                label="Channel Name"
                placeholder="Enter channel name"
                :disabled="isRenaming"
            />
        </form>

        <template #footer>
            <BaseButton variant="secondary" :disabled="isRenaming" @click="cancelRename">Cancel</BaseButton>
            <BaseButton
                variant="primary"
                :loading="isRenaming"
                :disabled="!newChannelName.trim() || isRenaming"
                @click="confirmRename"
            >
                Rename
            </BaseButton>
        </template>
    </BaseModal>
</template>
