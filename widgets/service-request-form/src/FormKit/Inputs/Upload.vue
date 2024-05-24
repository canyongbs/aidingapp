<script setup>
import { onMounted, ref, watch } from "vue";
import { createMessage } from "@formkit/core";
import { consumer } from "../../../../../portals/knowledge-management/src/Services/Consumer.js";
import axios from "axios";

const props = defineProps({
    context: Object,
});

const field = ref(null);
const id = ref(Math.random().toString(36));

// console.log(props, props.context.node, field);

// watch(field, (value) => {
//     console.log(value);
//     // props.context.node.input(value);
// })

onMounted(() => {
    // console.log(field, field.value.node.address);

    field.value.node.on('input', ({ payload }) => {
        props.context.node.store.filter(() => false);

        // console.log(payload);

        if (payload.length > props.context.limit) {
            props.context.node.store.set(createMessage({
                blocking: true,
                key: 'limit',
                value: `You can only upload a maximum of ${props.context.limit} files.`,
            }));

            return;
        }

        const size = props.context.size * 1024 * 1024;

        const uploads = [];

        payload.forEach((value, index) => {
            if (value.file.size > size) {
                props.context.node.store.set(createMessage({
                    blocking: true,
                    key: `size.${index}`,
                    value: `The file size of ${value.file.name} exceeds the maximum size of ${props.context.size}MB.`,
                }));

                return;
            }

            uploads.push(processUpload(value.file, index));

            // props.context.node.store.set(createMessage({
            //     blocking: true,
            //     key: `uploading.${index}`,
            //     value: `Uploading ${value.file.name}...`,
            // }));
            //
            // processUpload(value.file, index)
            //     .then((path) => {
            //         console.log(path);
            //         if (path) {
            //             props.context.node.store.set(createMessage({
            //                 type: 'success',
            //                 key: `uploaded.${index}`,
            //                 value: `Uploaded ${value.file.name} successfully.`,
            //             }));
            //             files.push(path);
            //         } else {
            //             props.context.node.store.set(createMessage({
            //                 blocking: true,
            //                 key: `uploaded.${index}`,
            //                 value: `Failed to upload ${value.file.name}.`,
            //             }));
            //         }
            //     });
            //
            // props.context.node.store.filter((message) => {
            //     console.log(`uploading.${index}`, message);
            //     return message.key === `uploading.${index}`;
            // });
            // console.log(props.context.node.store);
        });

        Promise.all(uploads)
            .then((files) => {
                props.context.node.input(files);
            });

        // props.context.node.input(files);
    });

    // field.value.node.hook.submit((payload, next) => {
    //     console.log(payload, next);
    //
    //     const address = field.value.node.address;
    //     address.shift();
    //     for
    //
    //     return next(payload);
    // });

    // props.context.node.at('$root').hook.submit((payload, next) => {
    //
    //     console.log(payload, next);
    //
    //     const address = field.value.node.address;
    //     address.shift();
    //     const path = address.join('.');
    //     // console.log(payload.`${path}`);
    //
    //     console.log(address.join('.'), payload, eval(`payload.${address.join('.')}`));
    //
    //     return next(payload);
    // });

    const processUpload = async (file, index) => {
        const { get } = consumer();

        props.context.node.store.set(createMessage({
            blocking: true,
            key: `uploading.${index}`,
            value: `Uploading ${file.name}...`,
        }));

        return get(props.context.uploadUrl, {
            filename: file.name,
        }).then(async (response) => {
            const { url, path } = response.data;

            return axios.put(url, file, {
                    headers: {
                        'Content-Type': file.type,
                    },
                }).then(() => {
                    props.context.node.store.set(createMessage({
                        type: 'success',
                        key: `uploaded.${index}`,
                        value: `Uploaded ${file.name} successfully.`,
                    }));

                    return {
                        originalFileName: file.name,
                        path: path,
                    };
                }).catch(() => {
                    props.context.node.store.set(createMessage({
                        blocking: true,
                        key: `uploaded.${index}`,
                        value: `Failed to upload ${file.name}.`,
                    }));

                    return null;
                });
        }).catch(() => {
            props.context.node.store.set(createMessage({
                blocking: true,
                key: `uploaded.${index}`,
                value: `Failed to upload ${file.name}.`,
            }));

            return null;
        }).finally(() => {
            props.context.node.store.remove(`uploading.${index}`);
        });
    };
});

</script>

<template>
    <FormKit ref="field" :id="id" type="file" :accept="context.accept" :multiple="context.multiple" />
</template>

<style scoped>

</style>
