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
    import { computed, useAttrs } from 'vue';

    defineOptions({ inheritAttrs: false });

    const PALETTE = {
        primary: {
            base: 'bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-600/10',
            hover: 'hover:bg-brand-100 hover:text-brand-800',
        },
        danger: {
            base: 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10',
            hover: 'hover:bg-red-100 hover:text-red-800',
        },
        success: {
            base: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/10',
            hover: 'hover:bg-green-100 hover:text-green-800',
        },
        warning: {
            base: 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/10',
            hover: 'hover:bg-amber-100 hover:text-amber-800',
        },
        info: {
            base: 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/10',
            hover: 'hover:bg-sky-100 hover:text-sky-800',
        },
        gray: {
            base: 'bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-600/10',
            hover: 'hover:bg-gray-100 hover:text-gray-700',
        },
        'transparent-white': {
            base: 'bg-white/15 text-white/90 ring-1 ring-inset ring-white/25',
            hover: 'hover:bg-white/25 hover:text-white hover:ring-white/40',
        },
        blue: {
            base: 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/10',
            hover: 'hover:bg-blue-100 hover:text-blue-800',
        },
        green: {
            base: 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/10',
            hover: 'hover:bg-green-100 hover:text-green-800',
        },
        red: {
            base: 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10',
            hover: 'hover:bg-red-100 hover:text-red-800',
        },
        orange: {
            base: 'bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/10',
            hover: 'hover:bg-orange-100 hover:text-orange-800',
        },
        amber: {
            base: 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/10',
            hover: 'hover:bg-amber-100 hover:text-amber-800',
        },
        yellow: {
            base: 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/10',
            hover: 'hover:bg-yellow-100 hover:text-yellow-800',
        },
        lime: {
            base: 'bg-lime-50 text-lime-700 ring-1 ring-inset ring-lime-600/10',
            hover: 'hover:bg-lime-100 hover:text-lime-800',
        },
        emerald: {
            base: 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/10',
            hover: 'hover:bg-emerald-100 hover:text-emerald-800',
        },
        teal: {
            base: 'bg-teal-50 text-teal-700 ring-1 ring-inset ring-teal-600/10',
            hover: 'hover:bg-teal-100 hover:text-teal-800',
        },
        cyan: {
            base: 'bg-cyan-50 text-cyan-700 ring-1 ring-inset ring-cyan-600/10',
            hover: 'hover:bg-cyan-100 hover:text-cyan-800',
        },
        sky: {
            base: 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/10',
            hover: 'hover:bg-sky-100 hover:text-sky-800',
        },
        indigo: {
            base: 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/10',
            hover: 'hover:bg-indigo-100 hover:text-indigo-800',
        },
        violet: {
            base: 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-600/10',
            hover: 'hover:bg-violet-100 hover:text-violet-800',
        },
        purple: {
            base: 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/10',
            hover: 'hover:bg-purple-100 hover:text-purple-800',
        },
        fuchsia: {
            base: 'bg-fuchsia-50 text-fuchsia-700 ring-1 ring-inset ring-fuchsia-600/10',
            hover: 'hover:bg-fuchsia-100 hover:text-fuchsia-800',
        },
        pink: {
            base: 'bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/10',
            hover: 'hover:bg-pink-100 hover:text-pink-800',
        },
        rose: {
            base: 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/10',
            hover: 'hover:bg-rose-100 hover:text-rose-800',
        },
    };

    const SIZE_CLASSES = {
        xs: 'min-w-4 px-0.5 py-0 text-xs tracking-tighter',
        sm: 'min-w-5 px-1.5 py-0.5 text-xs tracking-tight',
        md: 'min-w-6 px-2 py-1 text-xs',
    };

    const ICON_SIZE_CLASSES = {
        xs: 'h-2.5 w-2.5',
        sm: 'h-3 w-3',
        md: 'h-3 w-3',
    };

    const props = defineProps({
        tag: {
            type: String,
            default: 'span',
            validator: (v) => ['span', 'button', 'a', 'router-link'].includes(v),
        },
        type: {
            type: String,
            default: 'button',
        },
        href: {
            type: String,
            default: null,
        },
        to: {
            type: [String, Object],
            default: null,
        },
        target: {
            type: String,
            default: null,
        },
        color: {
            type: String,
            default: 'primary',
        },
        size: {
            type: String,
            default: 'md',
            validator: (v) => ['xs', 'sm', 'md'].includes(v),
        },
        icon: {
            type: [Object, Function],
            default: null,
        },
        iconPosition: {
            type: String,
            default: 'before',
            validator: (v) => ['before', 'after'].includes(v),
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['click']);

    const attrs = useAttrs();

    const componentTag = computed(() => props.tag);

    const isInteractive = computed(() => props.tag !== 'span');
    const isDisabled = computed(() => props.disabled && isInteractive.value);

    const palette = computed(() => PALETTE[props.color] ?? PALETTE.gray);

    const badgeClasses = computed(() => [
        'inline-flex items-center justify-center gap-x-1 rounded-md font-medium truncate',
        SIZE_CLASSES[props.size],
        palette.value.base,
        isInteractive.value &&
            !isDisabled.value && [
                palette.value.hover,
                'transition duration-75 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-current/50',
            ],
        isDisabled.value && 'cursor-default opacity-70 pointer-events-none',
    ]);

    const iconClasses = computed(() => ['shrink-0', ICON_SIZE_CLASSES[props.size]]);

    const componentAttrs = computed(() => {
        const base = {
            ...attrs,
            'aria-disabled': isDisabled.value ? 'true' : undefined,
        };

        if (props.tag === 'button') {
            return { ...base, type: props.type, disabled: isDisabled.value || undefined };
        }

        if (props.tag === 'a') {
            return {
                ...base,
                href: props.href,
                target: props.target,
                rel: props.target === '_blank' ? 'noopener noreferrer' : undefined,
                tabindex: isDisabled.value ? -1 : attrs.tabindex,
            };
        }

        if (props.tag === 'router-link') {
            return { ...base, to: props.to, tabindex: isDisabled.value ? -1 : attrs.tabindex };
        }

        return base;
    });

    function handleClick(event) {
        if (isDisabled.value) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }
        emit('click', event);
    }
</script>

<template>
    <component :is="componentTag" v-bind="componentAttrs" :class="badgeClasses" @click="handleClick">
        <component v-if="icon && iconPosition === 'before'" :is="icon" :class="iconClasses" aria-hidden="true" />

        <span class="min-w-0 truncate">
            <slot />
        </span>

        <component v-if="icon && iconPosition === 'after'" :is="icon" :class="iconClasses" aria-hidden="true" />
    </component>
</template>
