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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
    import axios from 'axios';
    import { computed, ref } from 'vue';
    import { consumer } from '../../../../Services/Consumer.js';
    import { useAssistantStore } from '../../../../Stores/assistant.js';

    const props = defineProps({
        fieldId: {
            type: String,
            required: true,
        },
        config: {
            type: Object,
            default: () => ({}),
        },
        required: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: '',
        },
    });

    const emit = defineEmits(['submit', 'cancel']);

    const { apiUrl } = useAssistantStore();

    const files = ref([]);
    const isUploading = ref(false);
    const error = ref('');

    const acceptedTypes = computed(() => props.config.accept || []);
    const maxFiles = computed(() => props.config.limit || 5);
    const maxSizeMB = computed(() => props.config.size || 10);

    const uploadedFiles = computed(() => files.value.filter((f) => f.status === 'uploaded'));
    const hasUploadedFiles = computed(() => uploadedFiles.value.length > 0);

    const displayText = computed(() => {
        const count = uploadedFiles.value.length;
        if (count === 0) return '';
        if (count === 1) return `Uploaded: ${uploadedFiles.value[0].name}`;
        return `Uploaded ${count} file(s)`;
    });

    const handleFileSelect = async (event) => {
        const selectedFiles = Array.from(event.target.files || []);

        if (files.value.length + selectedFiles.length > maxFiles.value) {
            error.value = `Maximum ${maxFiles.value} files allowed`;
            return;
        }

        for (const file of selectedFiles) {
            if (file.size > maxSizeMB.value * 1024 * 1024) {
                error.value = `File ${file.name} exceeds maximum size of ${maxSizeMB.value}MB`;
                continue;
            }

            if (acceptedTypes.value.length > 0 && !acceptedTypes.value.some((type) => file.type.match(type))) {
                error.value = `File type not accepted: ${file.name}`;
                continue;
            }

            await uploadFile(file);
        }

        event.target.value = '';
    };

    const uploadFile = async (file) => {
        const fileEntry = {
            id: Date.now() + Math.random(),
            name: file.name,
            size: file.size,
            status: 'uploading',
            progress: 0,
            path: null,
            error: null,
        };

        files.value.push(fileEntry);
        isUploading.value = true;
        error.value = '';

        try {
            const { get } = consumer();
            const uploadUrlEndpoint = apiUrl + '/service-request/request-upload-url';

            const response = await get(uploadUrlEndpoint, { filename: file.name });
            const { url, path } = response.data;

            await axios.put(url, file, {
                headers: { 'Content-Type': file.type },
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    fileEntry.progress = percentCompleted;
                },
            });

            fileEntry.status = 'uploaded';
            fileEntry.path = path;
        } catch (err) {
            fileEntry.status = 'error';
            fileEntry.error = 'Upload failed';
            console.error('Upload error:', err);
        } finally {
            isUploading.value = files.value.some((f) => f.status === 'uploading');
        }
    };

    const removeFile = (fileId) => {
        const index = files.value.findIndex((f) => f.id === fileId);
        if (index !== -1) {
            files.value.splice(index, 1);
        }
    };

    const submit = () => {
        if (props.required && !hasUploadedFiles.value) {
            error.value = 'Please upload at least one file';
            return;
        }

        const fileData = uploadedFiles.value.map((f) => ({
            path: f.path,
            name: f.name,
            size: f.size,
        }));

        emit('submit', fileData, displayText.value);
    };

    const formatSize = (bytes) => {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    };
</script>

<template>
    <div class="space-y-3">
        <!-- File Input -->
        <label
            class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
        >
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                    />
                </svg>
                <p class="text-xs text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                <p class="text-xs text-gray-400">Max {{ maxSizeMB }}MB per file</p>
            </div>
            <input
                type="file"
                class="hidden"
                :multiple="maxFiles > 1"
                :accept="acceptedTypes.join(',')"
                @change="handleFileSelect"
                :disabled="isUploading"
            />
        </label>

        <!-- File List -->
        <div v-if="files.length > 0" class="space-y-2">
            <div
                v-for="file in files"
                :key="file.id"
                class="flex items-center gap-2 p-2 bg-white border border-gray-200 rounded-md"
            >
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-700 truncate">{{ file.name }}</p>
                    <p class="text-xs text-gray-400">{{ formatSize(file.size) }}</p>
                </div>

                <!-- Status -->
                <div class="flex items-center gap-2">
                    <div v-if="file.status === 'uploading'" class="flex items-center gap-2">
                        <div class="w-16 h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-brand-500 transition-all"
                                :style="{ width: file.progress + '%' }"
                            ></div>
                        </div>
                        <span class="text-xs text-gray-500">{{ file.progress }}%</span>
                    </div>

                    <svg
                        v-else-if="file.status === 'uploaded'"
                        class="w-5 h-5 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>

                    <svg
                        v-else-if="file.status === 'error'"
                        class="w-5 h-5 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>

                    <button
                        @click="removeFile(file.id)"
                        class="p-1 text-gray-400 hover:text-red-500"
                        :disabled="file.status === 'uploading'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <p v-if="error" class="text-xs text-red-600">{{ error }}</p>

        <div class="flex gap-2">
            <button
                @click="submit"
                :disabled="isUploading || (required && !hasUploadedFiles)"
                class="flex-1 px-3 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                {{ isUploading ? 'Uploading...' : 'Submit' }}
            </button>
            <button
                @click="emit('cancel')"
                :disabled="isUploading"
                class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors disabled:opacity-50"
            >
                Cancel
            </button>
        </div>
    </div>
</template>
