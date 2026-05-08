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
        outlined: {
            type: Boolean,
            default: false,
        },
        labelSrOnly: {
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

    // fi-size-* modifier class; 'md' is the default — no modifier class needed.
    const sizeClass = computed(() => {
        const map = { xs: 'fi-size-xs', sm: 'fi-size-sm', lg: 'fi-size-lg', xl: 'fi-size-xl' };

        return map[props.size] ?? null;
    });

    const iconSizeClass = computed(() => {
        return ['xs', 'sm'].includes(props.size) ? 'h-4 w-4' : 'h-5 w-5';
    });

    // Only colored buttons (non-gray) get fi-color + CSS custom properties.
    const isColored = computed(() => props.color !== 'gray');

    // Mirrors Filament ButtonComponent color mapping:
    // --bg/--text for normal state, --hover-bg/--hover-text for hover,
    // --dark-* variants for dark mode.
    const colorVars = computed(() => {
        if (!isColored.value) return {};

        const map = {
            primary: {
                '--bg': '#2563eb',
                '--text': '#ffffff',
                '--hover-bg': '#3b82f6',
                '--hover-text': '#ffffff',
                '--dark-bg': '#3b82f6',
                '--dark-text': '#ffffff',
                '--dark-hover-bg': '#60a5fa',
                '--dark-hover-text': '#ffffff',
                '--focus-ring': 'rgba(37, 99, 235, 0.5)',
            },
            danger: {
                '--bg': '#dc2626',
                '--text': '#ffffff',
                '--hover-bg': '#ef4444',
                '--hover-text': '#ffffff',
                '--dark-bg': '#ef4444',
                '--dark-text': '#ffffff',
                '--dark-hover-bg': '#f87171',
                '--dark-hover-text': '#ffffff',
                '--focus-ring': 'rgba(220, 38, 38, 0.5)',
            },
            info: {
                '--bg': '#0284c7',
                '--text': '#ffffff',
                '--hover-bg': '#0ea5e9',
                '--hover-text': '#ffffff',
                '--dark-bg': '#0ea5e9',
                '--dark-text': '#ffffff',
                '--dark-hover-bg': '#38bdf8',
                '--dark-hover-text': '#ffffff',
                '--focus-ring': 'rgba(2, 132, 199, 0.5)',
            },
            success: {
                '--bg': '#16a34a',
                '--text': '#ffffff',
                '--hover-bg': '#22c55e',
                '--hover-text': '#ffffff',
                '--dark-bg': '#22c55e',
                '--dark-text': '#ffffff',
                '--dark-hover-bg': '#4ade80',
                '--dark-hover-text': '#ffffff',
                '--focus-ring': 'rgba(22, 163, 74, 0.5)',
            },
            warning: {
                '--bg': '#d97706',
                '--text': '#ffffff',
                '--hover-bg': '#f59e0b',
                '--hover-text': '#ffffff',
                '--dark-bg': '#f59e0b',
                '--dark-text': '#fff7ed',
                '--dark-hover-bg': '#fbbf24',
                '--dark-hover-text': '#fff7ed',
                '--focus-ring': 'rgba(217, 119, 6, 0.5)',
            },
        };

        return map[props.color] ?? {};
    });

    // Assembles the fi-btn class list, mirroring Filament's ->class([...]) call.
    const buttonClasses = computed(() => [
        'fi-btn',
        sizeClass.value,
        isColored.value && 'fi-color',
        props.outlined && 'fi-outlined',
        isDisabled.value && 'fi-disabled',
    ]);

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
        return ['fi-icon shrink-0 transition duration-75', iconSizeClass.value];
    });
</script>

<template>
    <component
        :is="componentTag"
        v-bind="componentAttrs"
        :class="buttonClasses"
        :style="colorVars"
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

        <span v-if="hasLabel && labelSrOnly" class="sr-only"><slot /></span>
        <span v-else-if="hasLabel"><slot /></span>

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
