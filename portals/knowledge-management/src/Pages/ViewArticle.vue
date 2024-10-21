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
    import { defineProps, ref, watch, onMounted } from 'vue';
    import { useRoute } from 'vue-router';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import AppLoading from '../Components/AppLoading.vue';
    import { consumer } from '../Services/Consumer.js';
    import {
        Bars3Icon,
        ClockIcon,
        EyeIcon,
        HandThumbUpIcon,
        HandThumbDownIcon,
    } from '@heroicons/vue/24/outline/index.js';
    import DOMPurify from 'dompurify';
    import Tags from '../Components/Tags.vue';
    import { useAuthStore } from '../Stores/auth.js';

    const route = useRoute();

    const { get, post } = consumer();

    const props = defineProps({
        searchUrl: {
            type: String,
            required: true,
        },
        apiUrl: {
            type: String,
            required: true,
        },
        categories: {
            type: Object,
            required: true,
        },
    });

    const loading = ref(true);
    const category = ref(null);
    const article = ref(null);
    const portalViewCount = ref(0);
    const portalViewCountFlag = ref(false);
    const feedback = ref(null);
    const articalWasHelpfulFeatureFlag = ref(null);

    watch(
        route,
        function (newRouteValue) {
            getData();
        },
        {
            immediate: true,
        },
    );

    function getData() {
        loading.value = true;

        get(props.apiUrl + '/categories/' + route.params.categoryId + '/articles/' + route.params.articleId)
            .then((response) => {
                if (response.data) {
                    category.value = response.data.category;
                    article.value = response.data.article;
                    portalViewCount.value = response.data.portal_view_count;
                    articalWasHelpfulFeatureFlag.value = response.data.article_was_helpful_feature_flag;
                    feedback.value = response.data.article.vote ? response.data.article.vote.is_helpful : null;
                }

                loading.value = false;
            })
            .catch((error) => {
                if (error.response && error.response.status === 401) {
                    loading.value = false;
                } else {
                    console.log('An error occurred', error);
                }
            });
    }
    async function toggleFeedback(type) {
        await post(props.apiUrl + '/knowledge_base_article_vote/store', {
            articleVote: feedback.value === type ? null : type,
            articleId: route.params.articleId,
        })
            .then((response) => {
                if (response.status === 200 && !response.data) {
                    feedback.value = null;
                } else if (response.status === 200) {
                    feedback.value = response.data.is_helpful;
                }
            })
            .catch((error) => {
                console.error('Error submitting feedback:', error);
            });
    }
</script>

<template>
    <div class="sticky top-0 z-40 flex flex-col items-center bg-gray-50">
        <div class="w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div v-if="loading">
                    <AppLoading />
                </div>
                <div v-else>
                    <main class="flex flex-col gap-8">
                        <Breadcrumbs
                            :breadcrumbs="[
                                { name: category.name, route: 'view-category', params: { categoryId: category.id } },
                            ]"
                            currentCrumb="Articles"
                            v-if="article"
                        ></Breadcrumbs>

                        <div class="grid space-y-2">
                            <div class="flex flex-col gap-3">
                                <div class="prose max-w-none" v-if="article">
                                    <h1 class="font-semibold">{{ article.name }}</h1>
                                    <div class="flex mb-4">
                                        <div class="text-gray-500 flex items-center space-x-1 mr-2">
                                            <EyeIcon class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
                                            <span class="text-xs">{{ portalViewCount }} Views</span>
                                        </div>
                                        <div class="text-gray-500 flex items-center space-x-1">
                                            <ClockIcon class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
                                            <span class="text-xs">Last updated: {{ article.lastUpdated }}</span>
                                        </div>
                                    </div>
                                    <Tags :tags="article.tags" :featured="article.featured" />
                                    <hr class="my-4" />
                                    <div v-html="DOMPurify.sanitize(article.content)"></div>
                                    <div
                                        class="flex items-center mt-6 p-4 border rounded-lg"
                                        v-if="articalWasHelpfulFeatureFlag"
                                    >
                                        <p class="text-lg font-semibold mr-4">Was this content helpful?</p>
                                        <div class="flex space-x-2">
                                            <button
                                                @click="toggleFeedback(true)"
                                                :class="[
                                                    'px-4 py-2 flex items-center justify-center space-x-1.5 rounded-lg border-2 transition duration-200 focus:outline-none',
                                                    feedback === true
                                                        ? 'bg-gradient-to-br from-primary-500 to-primary-800 text-white border-primary-300'
                                                        : 'bg-white text-gray-700 border-gray-300',
                                                ]"
                                            >
                                                <HandThumbUpIcon
                                                    class="w-5 h-5"
                                                    :class="feedback === true ? 'text-white' : 'text-gray-600'"
                                                />
                                                <span class="text-sm">Yes</span>
                                            </button>
                                            <button
                                                @click="toggleFeedback(false)"
                                                :class="[
                                                    'px-4 py-2 flex items-center justify-center space-x-1.5 rounded-lg border-2 transition duration-200 focus:outline-none',
                                                    feedback === false
                                                        ? 'bg-gradient-to-br from-primary-500 to-primary-800 text-white border-primary-300'
                                                        : 'bg-white text-gray-700 border-gray-300',
                                                ]"
                                            >
                                                <HandThumbDownIcon
                                                    class="w-5 h-5"
                                                    :class="feedback === false ? 'text-white' : 'text-gray-600'"
                                                />
                                                <span class="text-sm">No</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center min-h-screen bg-gray-100" v-else>
                                    <div class="text-center">
                                        <p class="text-lg font-semibold text-gray-800">
                                            The link you are attempting to access is invalid.
                                        </p>
                                        <router-link
                                            :to="{ name: 'home' }"
                                            class="mt-4 inline-block px-4 py-2 text-white bg-gradient-to-br from-primary-500 to-primary-800 m-3 p-2 rounded"
                                        >
                                            Return Home
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
