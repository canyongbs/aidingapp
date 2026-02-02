<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';

    const { user } = useAuthStore();
    const { hasServiceManagement } = useFeatureStore();
</script>

<template>
    <div class="flex-1 bg-gray-50">
        <div class="bg-[linear-gradient(to_right_bottom,rgba(var(--primary-500),1),rgba(var(--primary-800),1))] px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div class="text-right" v-if="hasServiceManagement && user">
                    <router-link :to="{ name: 'create-service-request' }">
                        <button
                            class="px-3 py-2 font-medium text-sm rounded bg-white text-brand-700 dark:text-brand-400"
                        >
                            New Request
                        </button>
                    </router-link>
                </div>

                <div class="flex flex-col gap-y-1 text-left">
                    <h3 class="text-3xl font-semibold text-white"><slot name="heading" /></h3>
                    <div class="text-brand-100"><slot name="description" /></div>
                </div>

                <div v-if="$slots.belowHeaderContent">
                    <slot name="belowHeaderContent" />
                </div>
            </div>
        </div>

        <div class="xl:px-6 max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <div class="px-6 xl:px-0" v-if="$slots.breadcrumbs">
                <slot name="breadcrumbs" />
            </div>

            <div class="ring-1 ring-black/5 shadow-sm xl:-mx-6 px-6 py-4 flex flex-col gap-y-6 xl:rounded bg-white">
                <slot />
            </div>
        </div>
    </div>
</template>
