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
    import { computed, useAttrs, useSlots } from 'vue';
    import LoadingIndicator from './LoadingIndicator.vue';

    const COLOR_VARS = {
        primary: {
            '--bg': 'rgba(var(--primary-600), 1)',
            '--text': '#ffffff',
            '--hover-bg': 'rgba(var(--primary-500), 1)',
            '--hover-text': '#ffffff',
            '--focus-ring': 'rgba(var(--primary-500), 0.5)',
        },
        danger: {
            '--bg': 'var(--color-red-600)',
            '--text': '#ffffff',
            '--hover-bg': 'var(--color-red-500)',
            '--hover-text': '#ffffff',
            '--focus-ring': 'color-mix(in srgb, var(--color-red-500) 50%, transparent)',
        },
        info: {
            '--bg': 'var(--color-sky-600)',
            '--text': '#ffffff',
            '--hover-bg': 'var(--color-sky-500)',
            '--hover-text': '#ffffff',
            '--focus-ring': 'color-mix(in srgb, var(--color-sky-500) 50%, transparent)',
        },
        success: {
            '--bg': 'var(--color-green-600)',
            '--text': '#ffffff',
            '--hover-bg': 'var(--color-green-500)',
            '--hover-text': '#ffffff',
            '--focus-ring': 'color-mix(in srgb, var(--color-green-500) 50%, transparent)',
        },
        warning: {
            '--bg': 'var(--color-amber-600)',
            '--text': '#ffffff',
            '--hover-bg': 'var(--color-amber-500)',
            '--hover-text': '#ffffff',
            '--focus-ring': 'color-mix(in srgb, var(--color-amber-500) 50%, transparent)',
        },
    };

    const SIZE_CLASSES = {
        xs: 'gap-1 px-2 py-1.5 text-xs',
        sm: 'gap-1 px-2.5 py-1.5',
        md: 'px-3 py-2',
        lg: 'gap-1.5 px-3.5 py-2.5',
        xl: 'gap-1.5 px-4 py-3',
    };

    const ICON_ONLY_SIZE_CLASSES = {
        xs: 'p-1.5',
        sm: 'p-1.5',
        md: 'p-2',
        lg: 'p-2.5',
        xl: 'p-3',
    };

    defineOptions({
        inheritAttrs: false,
    });

    const props = defineProps({
        tag: {
            type: String,
            default: 'button',
            validator: (value) => ['button', 'a', 'router-link'].includes(value),
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
            validator: (value) => ['primary', 'danger', 'gray', 'info', 'success', 'warning'].includes(value),
        },
        size: {
            type: String,
            default: 'md',
            validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value),
        },
        icon: {
            type: [Object, Function],
            default: null,
        },
        iconPosition: {
            type: String,
            default: 'before',
            validator: (value) => ['before', 'after'].includes(value),
        },
        iconOnly: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        loadingText: {
            type: String,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['click']);

    const attrs = useAttrs();
    const slots = useSlots();

    const isDisabled = computed(() => props.disabled || props.loading);

    const componentTag = computed(() => props.tag);

    const colorVars = computed(() => (props.color === 'gray' ? {} : (COLOR_VARS[props.color] ?? {})));

    const sizeClass = computed(() => (props.iconOnly ? ICON_ONLY_SIZE_CLASSES[props.size] : SIZE_CLASSES[props.size]));

    const colorClass = computed(() =>
        props.color === 'gray' ? 'bg-white text-gray-950 ring-1 ring-gray-950/10' : 'bg-(--bg) text-(--text)',
    );

    const stateClass = computed(() => {
        if (isDisabled.value) {
            return 'cursor-default opacity-70 pointer-events-none';
        }

        if (props.color === 'gray') {
            return 'hover:bg-gray-50 focus-visible:ring-2';
        }

        return 'hover:bg-(--hover-bg) hover:text-(--hover-text) focus-visible:ring-2 focus-visible:ring-[var(--focus-ring)]';
    });

    const buttonClasses = computed(() => [
        'relative inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg text-sm font-medium transition duration-75 outline-none',
        sizeClass.value,
        colorClass.value,
        stateClass.value,
    ]);

    const buttonStyle = computed(() => ({
        ...colorVars.value,
        'border-radius': 'var(--rounding-md, 0.5rem)',
    }));

    const iconClasses = computed(() => [
        'shrink-0 transition duration-75',
        props.color === 'gray' && 'text-gray-400',
        ['xs', 'sm'].includes(props.size) ? 'h-4 w-4' : 'h-5 w-5',
    ]);

    const hasLabel = computed(() => !props.iconOnly && (!!slots.default || (props.loading && !!props.loadingText)));

    const componentAttrs = computed(() => {
        const base = {
            ...attrs,
            'aria-busy': props.loading || undefined,
            'aria-disabled': isDisabled.value ? 'true' : undefined,
        };

        if (props.tag === 'button') {
            return { ...base, type: props.type, disabled: isDisabled.value };
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

        return { ...base, to: props.to, tabindex: isDisabled.value ? -1 : attrs.tabindex };
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
    <component
        :is="componentTag"
        v-bind="componentAttrs"
        :class="buttonClasses"
        :style="buttonStyle"
        @click="handleClick"
    >
        <template v-if="iconPosition === 'before' && (loading || icon)">
            <LoadingIndicator v-if="loading" :class="[...iconClasses, 'animate-spin']" aria-hidden="true" />

            <component v-else-if="icon" :is="icon" :class="iconClasses" aria-hidden="true" />
        </template>

        <span v-if="hasLabel">
            <template v-if="loading && loadingText">{{ loadingText }}</template>
            <slot v-else />
        </span>

        <template v-if="iconPosition === 'after' && (loading || icon)">
            <LoadingIndicator v-if="loading" :class="[...iconClasses, 'animate-spin']" aria-hidden="true" />

            <component v-else-if="icon" :is="icon" :class="iconClasses" aria-hidden="true" />
        </template>
    </component>
</template>
