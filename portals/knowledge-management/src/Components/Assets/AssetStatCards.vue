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
    defineProps({
        counts: {
            type: Object,
            required: true,
        },
    });
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-2">
        <div class="p-5 bg-white rounded-[var(--rounding-lg)] border border-gray-200 shadow-xs">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Total</p>
                    <p class="text-4xl font-bold leading-none tabular-nums text-gray-800">
                        {{ counts.total }}
                    </p>
                </div>
                <span
                    class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-[rgba(var(--primary-50),1)]"
                >
                    <svg
                        class="w-5 h-5 text-[rgba(var(--primary-500),1)]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
                        />
                    </svg>
                </span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-[var(--rounding-lg)] border border-gray-200 shadow-xs">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Checked Out</p>
                    <p class="text-4xl font-bold leading-none tabular-nums text-gray-800">
                        {{ counts.checked_out }}
                    </p>
                </div>
                <span class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-orange-50">
                    <svg
                        class="w-5 h-5 text-orange-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"
                        />
                    </svg>
                </span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-[var(--rounding-lg)] border border-gray-200 shadow-xs">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Returned</p>
                    <p class="text-4xl font-bold leading-none tabular-nums text-gray-800">
                        {{ counts.returned }}
                    </p>
                </div>
                <span class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-green-50">
                    <svg
                        class="w-5 h-5 text-green-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"
                        />
                    </svg>
                </span>
            </div>
        </div>
    </div>
</template>
