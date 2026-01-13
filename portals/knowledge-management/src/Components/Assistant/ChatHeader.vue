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
    import { ChatBubbleLeftRightIcon, XMarkIcon } from '@heroicons/vue/24/outline';

    defineProps({
        showNewRequestLink: {
            type: Boolean,
            default: false,
        },
    });

    defineEmits(['close', 'new-request']);
</script>

<template>
    <div
        class="bg-gradient-to-r from-brand-600 to-brand-700 text-white px-6 py-4 flex items-center justify-between shadow-md shrink-0"
    >
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg">
                <ChatBubbleLeftRightIcon class="w-5 h-5" />
            </div>
            <div>
                <h2 class="text-lg font-semibold tracking-tight leading-tight">Support Assistant</h2>
                <button
                    v-if="showNewRequestLink"
                    @click="$emit('new-request')"
                    class="text-xs text-white/70 hover:text-white hover:underline transition-colors leading-tight"
                >
                    New request
                </button>
            </div>
        </div>
        <button
            @click="$emit('close')"
            class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
            aria-label="Close chat"
        >
            <XMarkIcon class="w-5 h-5" />
        </button>
    </div>
</template>
