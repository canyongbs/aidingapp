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
    import * as HeroiconsMini from '@heroicons/vue/20/solid';
    import * as HeroiconsOutline from '@heroicons/vue/24/outline';
    import * as HeroiconsSolid from '@heroicons/vue/24/solid';
    import { computed, useAttrs, useSlots } from 'vue';
    import { RouterLink } from 'vue-router';

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
            type: [String, Object, Function],
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
        disabled: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['click']);

    const attrs = useAttrs();
    const slots = useSlots();

    const isDisabled = computed(() => props.disabled || props.loading);

    const componentTag = computed(() => {
        if (props.tag === 'router-link') {
            return RouterLink;
        }

        return props.tag;
    });

    const sizeClasses = computed(() => {
        const sizes = {
            xs: 'gap-1 px-2 py-1.5 text-xs',
            sm: 'gap-1 px-2.5 py-1.5 text-sm',
            md: 'gap-1.5 px-3 py-2 text-sm',
            lg: 'gap-1.5 px-3.5 py-2.5 text-sm',
            xl: 'gap-1.5 px-4 py-3 text-sm',
        };

        return sizes[props.size];
    });

    const iconSizeClasses = computed(() => {
        return ['xs', 'sm'].includes(props.size) ? 'h-4 w-4' : 'h-5 w-5';
    });

    const colorClasses = computed(() => {
        const neutral = {
            bg: '#ffffff',
            text: '#09090b',
            hoverBg: '#f9fafb',
            hoverText: '#09090b',
            ring: 'rgba(9, 9, 11, 0.1)',
            focusRing: 'rgba(156, 163, 175, 0.4)',
            iconText: '#9ca3af',
        };

        const colors = {
            primary: {
                bg: '#2563eb',
                text: '#ffffff',
                hoverBg: '#1d4ed8',
                hoverText: '#ffffff',
                ring: 'rgba(37, 99, 235, 0.5)',
                focusRing: 'rgba(59, 130, 246, 0.5)',
                iconText: '#ffffff',
            },
            danger: {
                bg: '#dc2626',
                text: '#ffffff',
                hoverBg: '#b91c1c',
                hoverText: '#ffffff',
                ring: 'rgba(220, 38, 38, 0.5)',
                focusRing: 'rgba(239, 68, 68, 0.5)',
                iconText: '#ffffff',
            },
            gray: neutral,
            info: {
                bg: '#0284c7',
                text: '#ffffff',
                hoverBg: '#0369a1',
                hoverText: '#ffffff',
                ring: 'rgba(2, 132, 199, 0.5)',
                focusRing: 'rgba(14, 165, 233, 0.5)',
                iconText: '#ffffff',
            },
            success: {
                bg: '#16a34a',
                text: '#ffffff',
                hoverBg: '#15803d',
                hoverText: '#ffffff',
                ring: 'rgba(22, 163, 74, 0.5)',
                focusRing: 'rgba(34, 197, 94, 0.5)',
                iconText: '#ffffff',
            },
            warning: {
                bg: '#f59e0b',
                text: '#451a03',
                hoverBg: '#d97706',
                hoverText: '#451a03',
                ring: 'rgba(245, 158, 11, 0.5)',
                focusRing: 'rgba(251, 191, 36, 0.5)',
                iconText: '#451a03',
            },
        };

        return colors[props.color] ?? colors.primary;
    });

    const colorVariableStyle = computed(() => {
        return {
            '--btn-bg': colorClasses.value.bg,
            '--btn-text': colorClasses.value.text,
            '--btn-hover-bg': colorClasses.value.hoverBg,
            '--btn-hover-text': colorClasses.value.hoverText,
            '--btn-ring': colorClasses.value.ring,
            '--btn-focus-ring': colorClasses.value.focusRing,
            '--btn-icon': colorClasses.value.iconText,
        };
    });

    const baseClasses = computed(() => {
        return [
            // Mirrors Filament fi-btn base styles and non-outlined color mode.
            'relative inline-grid grid-flow-col items-center justify-center rounded-lg font-medium transition duration-75 outline-none',
            'bg-[var(--btn-bg)] text-[var(--btn-text)] ring-1 ring-[var(--btn-ring)]',
            'hover:bg-[var(--btn-hover-bg)] hover:text-[var(--btn-hover-text)]',
            sizeClasses.value,
            isDisabled.value
                ? 'cursor-default opacity-70 pointer-events-none'
                : 'focus-visible:ring-2 focus-visible:ring-[var(--btn-focus-ring)]',
        ];
    });

    const resolvedIcon = computed(() => {
        if (!props.icon || typeof props.icon !== 'string') {
            return props.icon;
        }

        const toPascalCase = (value) => {
            return value
                .split('-')
                .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
                .join('');
        };

        const byPrefix = {
            m: HeroiconsMini,
            o: HeroiconsOutline,
            s: HeroiconsSolid,
        };

        if (props.icon.startsWith('heroicon-')) {
            const segments = props.icon.split('-');

            if (segments.length >= 3) {
                const prefix = segments[1];
                const iconName = `${toPascalCase(segments.slice(2).join('-'))}Icon`;

                if (byPrefix[prefix]?.[iconName]) {
                    return byPrefix[prefix][iconName];
                }
            }
        }

        return HeroiconsOutline[props.icon] ?? HeroiconsSolid[props.icon] ?? HeroiconsMini[props.icon] ?? null;
    });

    const hasIcon = computed(() => !!resolvedIcon.value || !!slots.icon);

    const beforeIconVisible = computed(() => hasIcon.value && props.iconPosition === 'before');
    const afterIconVisible = computed(() => hasIcon.value && props.iconPosition === 'after');

    const hasLabel = computed(() => !props.iconOnly && !!slots.default);

    const componentAttrs = computed(() => {
        const baseAttrs = {
            ...attrs,
            'aria-busy': props.loading || undefined,
            'aria-disabled': isDisabled.value ? 'true' : undefined,
        };

        if (props.tag === 'button') {
            return {
                ...baseAttrs,
                type: props.type,
                disabled: isDisabled.value,
            };
        }

        if (props.tag === 'a') {
            return {
                ...baseAttrs,
                href: props.href,
                target: props.target,
                rel: props.target === '_blank' ? 'noopener noreferrer' : undefined,
                tabindex: isDisabled.value ? -1 : attrs.tabindex,
            };
        }

        return {
            ...baseAttrs,
            to: props.to,
            tabindex: isDisabled.value ? -1 : attrs.tabindex,
        };
    });

    function handleClick(event) {
        if (isDisabled.value) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }

        emit('click', event);
    }

    const iconClasses = computed(() => {
        return ['fi-icon shrink-0 transition duration-75', iconSizeClasses.value, 'text-[var(--btn-icon)]'];
    });
</script>

<template>
    <component
        :is="componentTag"
        v-bind="componentAttrs"
        :class="baseClasses"
        :style="colorVariableStyle"
        @click="handleClick"
    >
        <template v-if="beforeIconVisible">
            <svg
                v-if="loading"
                :class="[...iconClasses, 'animate-spin']"
                fill="none"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    class="opacity-20"
                    d="M12 2C6.47715 2 2 6.47715 2 12H5C5 8.13401 8.13401 5 12 5V2Z"
                    fill="currentColor"
                />
                <path
                    class="opacity-100"
                    d="M22 12C22 6.47715 17.5228 2 12 2V5C15.866 5 19 8.13401 19 12H22Z"
                    fill="currentColor"
                />
            </svg>

            <slot v-else-if="$slots.icon" name="icon" />
            <component v-else :is="resolvedIcon" :class="iconClasses" aria-hidden="true" />
        </template>

        <span v-if="hasLabel"><slot /></span>

        <template v-if="afterIconVisible">
            <svg
                v-if="loading"
                :class="[...iconClasses, 'animate-spin']"
                fill="none"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    class="opacity-20"
                    d="M12 2C6.47715 2 2 6.47715 2 12H5C5 8.13401 8.13401 5 12 5V2Z"
                    fill="currentColor"
                />
                <path
                    class="opacity-100"
                    d="M22 12C22 6.47715 17.5228 2 12 2V5C15.866 5 19 8.13401 19 12H22Z"
                    fill="currentColor"
                />
            </svg>

            <slot v-else-if="$slots.icon" name="icon" />
            <component v-else :is="resolvedIcon" :class="iconClasses" aria-hidden="true" />
        </template>
    </component>
</template>
