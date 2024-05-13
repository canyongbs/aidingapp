<script setup>

import { ChevronRightIcon } from "@heroicons/vue/20/solid/index.js";
import Tags from "./Tags.vue";

defineProps({
    article: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <router-link
        :to="{
        name: 'view-article',
        params: {
            categoryId: article.categoryId,
            articleId: article.id
        },
    }"
        class="group p-3 grid space-y-2 text-sm font-medium text-gray-700"
    >
        <Tags :tags="article.tags" />

        <div class="flex">
            <span> {{ article.name }} </span>
            <ChevronRightIcon
                class="opacity-0 h-5 w-5 text-primary-600 transition-all group-hover:translate-x-2 group-hover:opacity-100"
            />
        </div>
    </router-link>
</template>

<style scoped>

</style>
