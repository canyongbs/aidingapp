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
    import { ChatBubbleLeftRightIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
    });

    const emit = defineEmits(['toggle']);
</script>

<template>
    <button
        @click="emit('toggle')"
        class="bg-[linear-gradient(to_right_bottom,rgba(var(--primary-500),1),rgba(var(--primary-800),1))] hover:bg-[linear-gradient(to_right_bottom,rgba(var(--primary-600),1),rgba(var(--primary-900),1))] text-white rounded-full p-4 shadow-xl hover:shadow-2xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-brand-500/50 hover:scale-105 active:scale-95"
        aria-label="Toggle chat assistant"
    >
        <ChatBubbleLeftRightIcon v-if="!props.isOpen" class="w-6 h-6" />
        <ChevronDownIcon v-else class="w-6 h-6" />
    </button>
</template>
