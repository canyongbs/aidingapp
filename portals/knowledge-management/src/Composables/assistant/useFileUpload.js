/*
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
*/
import axios from 'axios';
import { computed, ref } from 'vue';
import { useAssistantStore } from '../../Stores/assistant.js';

export function useFileUpload() {
    const { requestUploadUrl } = useAssistantStore();

    // Each file object: { id, file, originalName, path, progress, status, error }
    // status: 'pending' | 'uploading' | 'complete' | 'error'
    const files = ref([]);
    const isEnabled = ref(false);

    let fileIdCounter = 0;

    const enableAttachments = () => {
        isEnabled.value = true;
    };

    const disableAttachments = () => {
        isEnabled.value = false;
        files.value = [];
    };

    const addFiles = async (fileList) => {
        for (const file of fileList) {
            const fileEntry = {
                id: ++fileIdCounter,
                file,
                originalName: file.name,
                path: null,
                progress: 0,
                status: 'pending',
                error: null,
            };
            files.value.push(fileEntry);
            uploadFile(fileEntry);
        }
    };

    const uploadFile = async (fileEntry) => {
        try {
            fileEntry.status = 'uploading';

            // Get presigned URL from backend
            const response = await axios.get(requestUploadUrl, {
                params: { filename: fileEntry.originalName },
            });

            const { path, url, headers } = response.data;
            fileEntry.path = path;

            // Upload directly to S3 using presigned URL
            await axios.put(url, fileEntry.file, {
                headers: {
                    ...headers,
                    'Content-Type': fileEntry.file.type || 'application/octet-stream',
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        const newProgress = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                        fileEntry.progress = newProgress;
                    }
                },
            });

            fileEntry.status = 'complete';
            fileEntry.progress = 100;
        } catch (error) {
            console.error('[FileUpload] Upload failed:', error);
            fileEntry.status = 'error';
            fileEntry.error = error.message || 'Upload failed';
        }
    };

    const removeFile = (fileId) => {
        const index = files.value.findIndex((f) => f.id === fileId);
        if (index !== -1) {
            files.value.splice(index, 1);
        }
    };

    const getCompletedFileUrls = () => {
        return files.value
            .filter((f) => f.status === 'complete' && f.path)
            .map((f) => ({
                path: f.path,
                original_name: f.originalName,
            }));
    };

    const clearFiles = () => {
        files.value = [];
    };

    const hasFiles = () => files.value.length > 0;

    const allUploadsComplete = computed(() => {
        if (files.value.length === 0) return true;
        return files.value.every((f) => f.status === 'complete' || f.status === 'error');
    });

    return {
        files,
        isEnabled,
        enableAttachments,
        disableAttachments,
        addFiles,
        removeFile,
        getCompletedFileUrls,
        clearFiles,
        hasFiles,
        allUploadsComplete,
    };
}
