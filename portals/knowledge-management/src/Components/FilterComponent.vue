<script setup>
    import { defineProps, computed, ref, watch, defineEmits } from 'vue';

    const props = defineProps({
        selectedFilter: {
            type: String,
            default: '',
        },
    });

    const emit = defineEmits(['change-filter']);
    const currentFilter = ref(props.selectedFilter);

    const updateFilter = (value) => {
        emit('change-filter', value);
    };
</script>
<template>
    <ul class="mb-5 flex list-none flex-row flex-wrap border-b-0 ps-0" role="tablist" data-twe-nav-ref>
        <li
            role="presentation"
            @click="updateFilter('all-articles')"
            class="text-sm font-semibold text-primary-600 px-4 py-3 hover:border-primary-600 cursor-pointer"
            :class="
                selectedFilter === '' || selectedFilter === 'all-articles'
                    ? 'border-b-2 border-primary-600'
                    : 'border-transparent'
            "
            data-twe-toggle="pill"
            aria-selected="true"
        >
            All Articles
        </li>
        <li
            role="presentation"
            @click="updateFilter('featured')"
            class="text-sm font-semibold text-primary-600 px-4 py-3 border-b-2 hover:border-primary-600 cursor-pointer"
            :class="selectedFilter === 'featured' ? 'border-b-2 border-primary-600' : 'border-transparent'"
            data-twe-toggle="pill"
            aria-selected="true"
        >
            Featured
        </li>
        <li
            role="presentation"
            @click="updateFilter('most-viewed')"
            class="text-sm font-semibold text-primary-600 px-4 py-3 border-b-2 hover:border-primary-600 cursor-pointer"
            :class="selectedFilter === 'most-viewed' ? 'border-b-2 border-primary-600' : 'border-transparent'"
            data-twe-toggle="pill"
            aria-selected="true"
        >
            Most Viewed
        </li>
    </ul>
</template>
