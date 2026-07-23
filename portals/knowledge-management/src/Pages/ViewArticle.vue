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
    import truncate from 'lodash/truncate';
    import { computed, defineProps, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import BaseButton from '@common/BaseButton.vue';
    import AppLoading from '@common/portal/AppLoading.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import Page from '@common/portal/Page.vue';
    import PageCard from '@common/portal/PageCard.vue';
    import Tags from '@common/portal/Tags.vue';
    import ArticleAttachments from '@common/portal/article/ArticleAttachments.vue';
    import ArticleContent from '@common/portal/article/ArticleContent.vue';
    import ArticleFeedback from '@common/portal/article/ArticleFeedback.vue';
    import ArticleMeta from '@common/portal/article/ArticleMeta.vue';
    import { consumer } from '../Services/Consumer.js';

    const route = useRoute();
    const router = useRouter();

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
    const feedback = ref(null);
    const helpfulVotePercentage = ref(0);
    const parentCategory = ref(null);

    const breadcrumbs = computed(() => {
        if (article.value && category.value) {
            const breadcrumbsList = [];

            if (parentCategory.value) {
                breadcrumbsList.push({
                    name: parentCategory.value.name,
                    route: 'view-category',
                    params: { categorySlug: parentCategory.value.slug },
                });
            }

            breadcrumbsList.push({
                name: category.value.name,
                route: parentCategory.value ? 'view-subcategory' : 'view-category',
                params: parentCategory.value
                    ? { parentCategorySlug: parentCategory.value.slug, subCategorySlug: category.value.slug }
                    : { categorySlug: category.value.slug },
            });

            return breadcrumbsList;
        }

        return [];
    });

    const currentCrumb = computed(() => {
        return article.value
            ? truncate(article.value.name, {
                  length: 16,
              })
            : 'Not Found';
    });

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

        get(props.apiUrl + '/categories/' + route.params.categorySlug + '/articles/' + route.params.articleId)
            .then((response) => {
                if (response.data) {
                    if (response.data.category.slug !== route.params.categorySlug) {
                        router.replace({
                            name: 'view-article',
                            params: {
                                categorySlug: response.data.category.slug,
                                articleId: route.params.articleId,
                            },
                        });
                    }

                    category.value = response.data.category;
                    article.value = response.data.article;
                    parentCategory.value = response.data.category.parentCategory;
                    portalViewCount.value = response.data.portal_view_count;
                    feedback.value = response.data.article.vote ? response.data.article.vote.is_helpful : null;
                    helpfulVotePercentage.value = response.data.helpful_vote_percentage;
                }

                loading.value = false;
            })
            .catch((error) => {
                if (error.response && (error.response.status === 401 || error.response.status === 404)) {
                    loading.value = false;
                } else {
                    console.log('An error occurred', error);
                }
            });
    }
    async function toggleFeedback(type) {
        await post(props.apiUrl + '/knowledge_base_article_vote/store', {
            article_vote: feedback.value === type ? null : type,
            article_id: route.params.articleId,
        })
            .then((response) => {
                if (response.status === 200) {
                    if (response.data.hasOwnProperty('is_helpful') && response.data.is_helpful !== null) {
                        feedback.value = response.data.is_helpful;
                    } else {
                        feedback.value = null;
                    }
                }

                helpfulVotePercentage.value = response.data.helpful_vote_percentage;
            })
            .catch((error) => {
                console.error('Error submitting feedback:', error);
            });
    }
</script>

<template>
    <div v-if="loading">
        <AppLoading />
    </div>

    <Page v-if="!loading && category && article">
        <template #heading>
            {{ article.name }}
        </template>

        <template #description>
            <ArticleMeta :viewCount="portalViewCount" :lastUpdated="article.lastUpdated" />
        </template>

        <template #belowHeaderContent>
            <Tags :tags="article.tags" :featured="article.featured" variant="hero" />
        </template>

        <template #breadcrumbs>
            <Breadcrumbs :breadcrumbs="breadcrumbs" :currentCrumb="currentCrumb" />
        </template>

        <PageCard v-if="article.attachments && article.attachments.length > 0">
            <ArticleAttachments :attachments="article.attachments" />
        </PageCard>

        <PageCard>
            <ArticleContent v-code-blocks :content="article.content" />

            <ArticleFeedback
                :feedback="feedback"
                :helpfulVotePercentage="helpfulVotePercentage"
                @toggle-feedback="toggleFeedback"
            />
        </PageCard>
    </Page>

    <Page v-if="!loading && (!category || !article)">
        <template #heading> 404 Not Found </template>

        <PageCard>
            <EmptyState>
                <template #heading>Article Not Found</template>
                <template #description>The article you are looking for does not exist or has been removed.</template>
                <template #actions>
                    <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md">
                        Return Home
                    </BaseButton>
                </template>
            </EmptyState>
        </PageCard>
    </Page>
</template>
