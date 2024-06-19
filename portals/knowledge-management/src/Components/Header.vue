<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    import { defineProps } from 'vue';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';
    import { useTokenStore } from '../Stores/token.js';

    const { user, requiresAuthentication } = useAuthStore();
    const { hasServiceManagement } = useFeatureStore();
    const { removeToken } = useTokenStore();

    const props = defineProps({
        apiUrl: {
            type: String,
            required: true,
        },
        headerLogo: {
            type: String,
            required: true,
        },
        appName: {
            type: String,
            required: true,
        },
    });

    const logout = () => {
        const { post } = consumer();

        post(props.apiUrl + '/logout').then((response) => {
            if (!response.data.success) {
                return;
            }

            removeToken();
            window.location.href = response.data.redirect_url;
        });
    };
</script>

<template>
    <div class="header item-center bg-white">
        <div class="flex">
            <div class="flex-auto">
                <router-link :to="{ name: 'home' }">
                    <img :src="headerLogo" :alt="appName" class="max-h-20 m-3 max-w-64" />
                </router-link>
            </div>
            <div v-if="requiresAuthentication || hasServiceManagement">
                <button
                    v-if="user"
                    @click="logout"
                    type="button"
                    class="bg-gradient-to-br from-primary-500 to-primary-800 text-white text-sm font-medium float-right m-3 p-2 rounded"
                >
                    Sign out
                </button>
                <button
                    v-else
                    @click="$emit('showLogin')"
                    type="button"
                    class="bg-gradient-to-br from-primary-500 to-primary-800 text-white text-sm font-medium float-right m-3 p-2 rounded"
                >
                    Sign in
                </button>
            </div>
        </div>
    </div>
</template>
