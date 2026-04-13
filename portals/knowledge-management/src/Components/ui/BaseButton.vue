<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
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
    import { RouterLink } from 'vue-router';

    const props = defineProps({
        variant: {
            type: String,
            default: 'primary',
            validator: (v) => ['primary', 'secondary', 'ghost', 'success', 'neutral'].includes(v),
        },
        size: {
            type: String,
            default: 'md',
            validator: (v) => ['sm', 'md'].includes(v),
        },
        disabled: { type: Boolean, default: false },
        loading: { type: Boolean, default: false },
        // Marks the active item (e.g. current page). Keeps primary styling but removes interactivity.
        selected: { type: Boolean, default: false },
        iconLeft: { type: [Object, Function], default: null },
        iconOnly: { type: Boolean, default: false },
        type: { type: String, default: 'button' },
        as: {
            type: String,
            default: 'button',
            validator: (v) => ['button', 'router-link'].includes(v),
        },
        to: { type: [String, Object], default: null },
    });

    const isInert = computed(() => props.disabled || props.loading);

    const iconSizeClass = computed(() => ({ sm: 'h-3.5 w-3.5', md: 'h-5 w-5' })[props.size]);

    const sizeClasses = computed(() => {
        if (props.iconOnly) return { sm: 'p-1.5', md: 'p-2' }[props.size];
        return { sm: 'px-2.5 py-1.5 text-xs gap-1.5', md: 'px-3 py-2 text-sm gap-2' }[props.size];
    });

    const variantClasses = computed(() => {
        if (props.disabled) {
            return 'bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed opacity-60 pointer-events-none';
        }

        if (props.loading) {
            return 'bg-gray-100 text-gray-400 border border-gray-200 cursor-wait pointer-events-none';
        }

        // --primary-on-color is computed in App.vue from palette luminance (ensures contrast on light palettes).
        const primaryGradient =
            'bg-[linear-gradient(to_right_bottom,rgb(var(--primary-500)),rgb(var(--primary-800)))] text-(--primary-on-color,white) border border-transparent shadow-xs';

        if (props.selected) {
            return `${primaryGradient} cursor-default pointer-events-none`;
        }

        const variants = {
            primary: `${primaryGradient} hover:shadow-md hover:brightness-105 active:scale-[0.98] active:shadow-inner active:brightness-95 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[rgb(var(--primary-500))]`,
            secondary:
                'bg-white text-brand-700 border border-transparent shadow-xs hover:bg-brand-50 active:bg-brand-100 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-500',
            ghost: 'bg-transparent text-gray-600 border border-gray-300 hover:bg-gray-50 hover:text-gray-800 active:bg-gray-100 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-400',
            success:
                'bg-green-600 text-white border border-transparent hover:bg-green-700 active:bg-green-800 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-green-500',
            neutral:
                'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 active:bg-gray-100 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-400',
        };

        return variants[props.variant] ?? variants.primary;
    });

    const tag = computed(() => (props.as === 'router-link' ? RouterLink : 'button'));

    const elementAttrs = computed(() => {
        if (props.as === 'router-link') return { to: props.to };
        return { type: props.type, disabled: isInert.value };
    });
</script>

<template>
    <component
        :is="tag"
        v-bind="elementAttrs"
        class="inline-flex items-center justify-center font-medium rounded transition-all duration-200 focus-visible:outline-hidden select-none"
        :class="[sizeClasses, variantClasses]"
        :aria-disabled="isInert || selected ? true : undefined"
        :aria-busy="loading || undefined"
        :aria-current="selected ? 'true' : undefined"
    >
        <svg
            v-if="loading"
            class="animate-spin shrink-0"
            :class="iconSizeClass"
            fill="none"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
        <component v-else-if="iconLeft" :is="iconLeft" :class="iconSizeClass" class="shrink-0" aria-hidden="true" />
        <span v-if="!iconOnly"><slot /></span>
    </component>
</template>
