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
