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
    import { computed } from 'vue';

    const props = defineProps({
        // Semantic tone: 'success' | 'warning' | 'danger' | 'info' | 'primary' | 'neutral'
        tone: {
            type: String,
            default: 'neutral',
            validator: (value) => ['success', 'warning', 'neutral', 'danger', 'primary', 'info'].includes(value),
        },
        // Direct Tailwind color name (e.g. 'blue', 'amber'). Takes priority over tone.
        color: {
            type: String,
            default: null,
        },
        mono: { type: Boolean, default: false },
    });

    const TONE_MAP = {
        success: 'green',
        warning: 'orange',
        danger:  'red',
        info:    'blue',
        primary: 'primary',
        neutral: 'gray',
    };

    const PALETTE = {
        blue:    { container: 'bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-600/20' },
        sky:     { container: 'bg-sky-100 text-sky-800 ring-1 ring-inset ring-sky-600/20' },
        indigo:  { container: 'bg-indigo-100 text-indigo-800 ring-1 ring-inset ring-indigo-600/20' },
        cyan:    { container: 'bg-cyan-100 text-cyan-800 ring-1 ring-inset ring-cyan-600/20' },
        green:   { container: 'bg-green-100 text-green-800 ring-1 ring-inset ring-green-600/20' },
        emerald: { container: 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20' },
        teal:    { container: 'bg-teal-100 text-teal-800 ring-1 ring-inset ring-teal-600/20' },
        lime:    { container: 'bg-lime-100 text-lime-800 ring-1 ring-inset ring-lime-600/20' },
        amber:   { container: 'bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20' },
        yellow:  { container: 'bg-yellow-100 text-yellow-800 ring-1 ring-inset ring-yellow-600/20' },
        orange:  { container: 'bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-600/20' },
        red:     { container: 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-600/20' },
        rose:    { container: 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-600/20' },
        pink:    { container: 'bg-pink-100 text-pink-800 ring-1 ring-inset ring-pink-600/20' },
        fuchsia: { container: 'bg-fuchsia-100 text-fuchsia-800 ring-1 ring-inset ring-fuchsia-600/20' },
        purple:  { container: 'bg-purple-100 text-purple-800 ring-1 ring-inset ring-purple-600/20' },
        violet:  { container: 'bg-violet-100 text-violet-800 ring-1 ring-inset ring-violet-600/20' },
        gray:    { container: 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/20' },
        primary: { container: 'bg-[rgba(var(--primary-50),1)] text-[rgba(var(--primary-700),1)] ring-1 ring-inset ring-[rgba(var(--primary-600),0.2)]' },
    };

    const colorKey = computed(() => (props.color ?? TONE_MAP[props.tone] ?? 'gray').toLowerCase());

    const containerClasses = computed(() => {
        if (props.mono) return 'border border-gray-200 bg-gray-50 text-gray-600';
        return (PALETTE[colorKey.value] ?? PALETTE.gray).container;
    });
</script>

<template>
    <span
        :class="[
            'inline-flex items-center rounded-[var(--rounding-md)] text-xs',
            mono ? 'px-2 py-0.5 font-mono font-normal' : 'px-2.5 py-1 font-medium',
            containerClasses,
        ]"
    >
        <slot />
    </span>
</template>
