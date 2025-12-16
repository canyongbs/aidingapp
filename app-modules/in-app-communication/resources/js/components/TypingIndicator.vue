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
    import { computed } from 'vue';

    const props = defineProps({
        typingUsers: { type: Array, required: true },
        conversation: { type: Object, required: true },
        currentUserId: { type: String, required: true },
    });

    const typingNames = computed(() => {
        return props.typingUsers
            .filter((userId) => userId !== props.currentUserId)
            .map((userId) => {
                const participant = props.conversation.participants?.find(
                    (conversationParticipant) => conversationParticipant.participant_id === userId,
                );
                return participant?.participant?.name || 'Someone';
            });
    });

    const displayText = computed(() => {
        if (typingNames.value.length === 0) return '';
        if (typingNames.value.length === 1) {
            return `${typingNames.value[0]} is typing...`;
        }
        if (typingNames.value.length === 2) {
            return `${typingNames.value[0]} and ${typingNames.value[1]} are typing...`;
        }
        return 'Several people are typing...';
    });
</script>

<template>
    <div v-if="typingNames.length > 0" class="mt-2 flex items-center gap-2">
        <div class="flex space-x-1">
            <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.3s]" />
            <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.15s]" />
            <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-400" />
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ displayText }}</span>
    </div>
</template>
