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
    import { HomeIcon } from '@heroicons/vue/24/outline';
    import { useRouter } from 'vue-router';
    const router = useRouter();
    const goHome = () => {
        router.push({ name: 'home' });
    };
</script>

<template>
    <div class="bg-gray-50 flex flex-col overflow-hidden" style="height: calc(100vh - 160px)">
        <div class="flex-grow flex items-center justify-center overflow-hidden">
            <div class="w-full max-w-screen-xl mx-auto px-4">
                <div class="p-4 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">Coming Soon!</h1>
                    <p class="text-lg text-gray-600">
                        This feature is currently under development and will be available soon.
                    </p>
                    <button
                        @click="goHome"
                        class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded shadow text-white bg-gradient-to-br from-brand-500 to-brand-800 hover:from-brand-600 hover:to-brand-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
                    >
                        <HomeIcon class="h-5 w-5 mr-1" />
                        Return to Home
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
