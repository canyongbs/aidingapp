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
    import { ArrowLeftIcon } from '@heroicons/vue/16/solid';
    import { ArrowRightIcon } from '@heroicons/vue/20/solid';
    import { computed } from 'vue';
    import { useServiceRequestSubmit } from '../../composables/useServiceRequestSubmit.js';

    const props = defineProps({
        selectedType: { type: Object, required: true },
        selectedPriority: { type: String, default: '' },
        rawData: { type: Object, required: true },
    });

    const emit = defineEmits(['back', 'success']);

    const selectedPriorityObject = computed(
        () => props.selectedType.priorities?.find((p) => p.id === props.selectedPriority) ?? null,
    );

    const { title, description, attachments, isSubmitting, submitError, canSubmit, submitForm } =
        useServiceRequestSubmit(props.rawData.store_url_base, props.selectedType.id, props.selectedPriority);

    function onSubmit() {
        submitForm(() => emit('success', title.value));
    }
</script>

<template>
    <!-- Context bar -->
    <div
        class="shrink-0 mx-4 mt-4 mb-1 flex items-center gap-2 px-3 py-2 bg-brand-50 border border-brand-100 rounded-xl"
    >
        <button
            @click="$emit('back')"
            class="shrink-0 text-brand-400 hover:text-brand-600 transition-colors"
            aria-label="Back to type selection"
        >
            <ArrowLeftIcon class="w-4 h-4" />
        </button>
        <div class="flex-1 flex items-center gap-2 min-w-0 text-xs">
            <span class="text-brand-700 font-semibold truncate">{{ selectedType.name }}</span>
            <span class="text-brand-300 shrink-0">·</span>
            <span class="text-brand-500 truncate">{{ selectedPriorityObject?.name }}</span>
        </div>
    </div>

    <!-- Form -->
    <div class="flex-1 overflow-y-auto px-4 py-3 flex flex-col">
        <FormKit
            type="text"
            name="title"
            label="Title"
            placeholder="Brief summary of the issue"
            validation="required"
            :validation-messages="{ required: 'Title is required.' }"
            v-model="title"
            outer-class="!max-w-none"
            inner-class="!max-w-none !rounded-xl"
        />

        <FormKit
            type="textarea"
            name="description"
            label="Description"
            placeholder="Describe the issue in detail…"
            validation="required"
            :validation-messages="{ required: 'Description is required.' }"
            v-model="description"
            outer-class="!max-w-none"
            inner-class="!max-w-none !rounded-xl"
            input-class="!h-28"
        />

        <FormKit
            v-if="rawData?.upload_url"
            type="upload"
            name="attachments"
            label="Attachments"
            :upload-url="rawData.upload_url"
            :multiple="true"
            :accept="['*/*']"
            :limit="10"
            :size="25"
            v-model="attachments"
            outer-class="!max-w-none"
        />

        <!-- Submit error -->
        <div v-if="submitError" class="px-3 py-2.5 rounded-xl bg-red-50 border border-red-100 mt-2">
            <p class="text-sm text-red-600">{{ submitError }}</p>
        </div>
    </div>

    <!-- Submit footer -->
    <div class="shrink-0 px-4 pb-4 pt-3 border-t border-gray-100">
        <button
            @click="onSubmit"
            :disabled="!canSubmit"
            :class="[
                'w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-medium transition-all',
                canSubmit
                    ? 'bg-brand-500 hover:bg-brand-600 text-white shadow-sm'
                    : 'bg-gray-100 text-gray-300 cursor-not-allowed',
            ]"
        >
            <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
            </svg>
            {{ isSubmitting ? 'Submitting…' : 'Submit Service Request' }}
            <ArrowRightIcon v-if="!isSubmitting" class="w-4 h-4" />
        </button>
    </div>
</template>
