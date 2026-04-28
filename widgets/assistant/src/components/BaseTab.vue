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
    import { inject } from 'vue';

    const props = defineProps({
        value: { type: String, required: true },
        disabled: { type: Boolean, default: false },
    });

    const { activeValue, select } = inject('BaseTabs');
</script>

<template>
    <button
        :disabled="disabled"
        :title="disabled ? 'Coming soon' : undefined"
        :class="[
            'flex-1 text-sm font-medium py-2 transition-all',
            !disabled && activeValue === props.value ? 'bg-white text-gray-900 shadow-sm' : '',
            !disabled && activeValue !== props.value ? 'text-gray-500 hover:text-gray-700' : '',
            disabled ? 'text-gray-300 cursor-not-allowed' : '',
        ]"
        :style="{ borderRadius: 'calc(var(--rounding-lg) - 4px)' }"
        @click="!disabled && select(props.value)"
    >
        <slot />
    </button>
</template>
