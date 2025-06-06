<script setup>
    const props = defineProps({
        title: {
            type: String,
            required: true,
        },
        hasIssue: {
            type: Boolean,
            required: true,
        },
        message: {
            type: String,
            required: true,
        },
    });
</script>
<template>
    <div
        class="p-4 rounded-lg border"
        :class="{
            'text-emerald-800 border-emerald-400 bg-emerald-50': !hasIssue,
            'text-orange-800 border-orange-400 bg-orange-50': hasIssue,
        }"
    >
        <div class="flex items-center">
            <svg
                v-if="!hasIssue"
                class="shrink-0 text-emerald-800 me-1"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    fill-rule="evenodd"
                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                    clip-rule="evenodd"
                />
            </svg>

            <svg
                v-else
                class="shrink-0 text-orange-800 me-1"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    fill-rule="evenodd"
                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v5a1 1 0 1 0 2 0V8Zm-1 7a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H12Z"
                    clip-rule="evenodd"
                />
            </svg>

            <h3 class="text-lg font-medium">{{ title }}</h3>
        </div>
        <div class="mt-2 text-sm">
            {{ message }}
        </div>
    </div>
</template>
