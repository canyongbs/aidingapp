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
    import { onMounted, ref, watch } from 'vue';
    import { createMessage } from '@formkit/core';
    import { consumer } from '../../../../../portals/knowledge-management/src/Services/Consumer.js';
    import axios from 'axios';

    const props = defineProps({
        context: Object,
    });

    const field = ref(null);

    onMounted(() => {
        field.value.node.on('input', ({ payload }) => {
            props.context.node.store.filter(() => false);

            if (payload.length > props.context.limit) {
                props.context.node.store.set(
                    createMessage({
                        blocking: true,
                        key: 'limit',
                        value: `You can only upload a maximum of ${props.context.limit} files.`,
                    }),
                );

                return;
            }

            const size = props.context.size * 1000 * 1000;

            const uploads = [];

            for (const [index, value] of payload.entries()) {
                const extension = `.${value.file.name.split('.').pop()}`;

                if (!props.context.accept.includes(extension)) {
                    props.context.node.store.set(
                        createMessage({
                            blocking: true,
                            key: `extension.${index}`,
                            value: `The file extension ${extension} of ${value.file.name} is not supported.`,
                        }),
                    );

                    return;
                }

                if (value.file.size > size) {
                    props.context.node.store.set(
                        createMessage({
                            blocking: true,
                            key: `size.${index}`,
                            value: `The file size of ${value.file.name} exceeds the maximum size of ${props.context.size}MB.`,
                        }),
                    );

                    return;
                }
            }

            payload.forEach((value, index) => {
                uploads.push(processUpload(value.file, index));
            });

            Promise.all(uploads).then((files) => {
                props.context.node.input(files);
            });
        });

        const processUpload = async (file, index) => {
            const { get } = consumer();

            props.context.node.store.set(
                createMessage({
                    blocking: true,
                    key: `uploading.${index}`,
                    value: `Uploading ${file.name}...`,
                }),
            );

            return get(props.context.uploadUrl, {
                filename: file.name,
            })
                .then(async (response) => {
                    const { url, path } = response.data;

                    return axios
                        .put(url, file, {
                            headers: {
                                'Content-Type': file.type,
                            },
                        })
                        .then(() => {
                            props.context.node.store.set(
                                createMessage({
                                    type: 'success',
                                    key: `uploaded.${index}`,
                                    value: `Uploaded ${file.name} successfully.`,
                                }),
                            );

                            return {
                                originalFileName: file.name,
                                path: path,
                            };
                        })
                        .catch(() => {
                            props.context.node.store.set(
                                createMessage({
                                    blocking: true,
                                    key: `uploaded.${index}`,
                                    value: `Failed to upload ${file.name}.`,
                                }),
                            );

                            return null;
                        });
                })
                .catch(() => {
                    props.context.node.store.set(
                        createMessage({
                            blocking: true,
                            key: `uploaded.${index}`,
                            value: `Failed to upload ${file.name}.`,
                        }),
                    );

                    return null;
                })
                .finally(() => {
                    props.context.node.store.remove(`uploading.${index}`);
                });
        };
    });
</script>

<template>
    <FormKit ref="field" type="file" :accept="context.accept" :multiple="context.multiple" />
    <div :class="context.classes.help">Maximum number of files: {{ context.limit }}</div>
    <div :class="context.classes.help">Maximum file size: {{ context.size }} MB</div>
    <div :class="context.classes.help">Supported file extensions: {{ context.accept.join(', ') }}</div>
</template>

<style scoped></style>
