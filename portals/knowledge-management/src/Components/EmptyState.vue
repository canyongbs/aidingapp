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
    import { XMarkIcon } from '@heroicons/vue/24/outline/index.js';
</script>

<template>
    <div class="px-6 py-12">
        <div class="mx-auto grid max-w-lg justify-items-center text-center">
            <div class="mb-4 rounded-full bg-brand-100 p-3 dark:bg-brand-500/20">
                <XMarkIcon class="h-6 w-6 text-brand-500 dark:text-brand-400" />
            </div>

            <h4 class="text-base font-semibold leading-6 text-gray-950">
                <slot name="heading" />
            </h4>

            <p v-if="$slots.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                <slot name="description" />
            </p>

            <div v-if="$slots.actions" class="mt-6">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
