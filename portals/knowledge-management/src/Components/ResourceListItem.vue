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
    import { ChevronRightIcon } from '@heroicons/vue/20/solid';

    defineProps({
        to: {
            type: [Object, String],
            default: null,
        },
    });
</script>

<template>
    <li>
        <component
            :is="to ? 'router-link' : 'div'"
            :to="to || undefined"
            class="flex items-start px-6 py-3 text-sm text-gray-700 transition duration-75"
            :class="to ? 'group hover:bg-gray-50' : ''"
        >
            <div v-if="$slots.icon" class="mr-3 shrink-0 self-center">
                <slot name="icon" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-baseline gap-2 flex-wrap">
                    <span class="font-medium text-gray-950"><slot name="primary" /></span>
                    <span v-if="$slots.secondary" class="text-gray-500 text-sm"><slot name="secondary" /></span>
                </div>
                <p v-if="$slots.description" class="mt-0.5 text-sm text-gray-500">
                    <slot name="description" />
                </p>
            </div>
            <div v-if="$slots.badge" class="ml-3 shrink-0 self-start pt-0.5">
                <slot name="badge" />
            </div>
            <ChevronRightIcon
                v-if="to"
                class="ml-2 shrink-0 size-5 self-center text-gray-400 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100"
            />
        </component>
    </li>
</template>
