<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    import { createMessage } from '@formkit/core';
    import axios from 'axios';
    import { computed, onMounted, ref } from 'vue';
    import { consumer } from '../../../../../portals/knowledge-management/src/Services/Consumer.js';

    // import { FilePondFile, FilePondOptions, FilePondServerConfigProps } from 'filepond'
    import vueFilePond from 'vue-filepond';

    import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
    import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview';

    import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
    import 'filepond/dist/filepond.min.css';

    const FilePond = vueFilePond(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize,
    );

    const props = defineProps({
        context: Object,
    });

    const field = ref(null);
    const uploadedFiles = ref([]);
    const fileIndexCounter = ref(0);

    const serverOptions = computed(() => ({
        process: async (fieldName, file, metadata, load, error, progress, abort) => {
            console.log(`Uploading: ${file.name}`, fieldName, file, metadata, load, error, progress, abort);
            const index = fileIndexCounter.value++;
            try {
                // const { data } = await axios.get(props.context.uploadUrl, {
                //     params: { filename: file.name },
                // });

                // const { url, path } = data;

                // await axios.put(url, file, {
                //     headers: { "Content-Type": file.type },
                //     onUploadProgress: (e) => {
                //         progress(e.lengthComputable, e.loaded, e.total);
                //     },
                // });
                await axios
                    .get(props.context.uploadUrl, {
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

                console.log(`Upload successful: ${file.name}`);
                load(path);
                uploadedFiles.value.push({ source: path, options: { type: 'local' } });
            } catch (err) {
                console.error('Upload error:', err);
                error('Upload failed');
            }

            return {
                abort: () => {
                    console.log('Upload aborted');
                    abort();
                },
            };
        },
        revert: async (uniqueFileId, load) => {
            console.log(`Reverting file: ${uniqueFileId}`);
            try {
                await axios.delete('/api/remove-file', { data: { filePath: uniqueFileId } });
                uploadedFiles.value = uploadedFiles.value.filter((f) => f.source !== uniqueFileId);
                load();
            } catch (err) {
                console.error('Failed to remove file:', err);
            }
        },
        // load: async (source, load, error, progress, abort) => {
        //     console.log(`Loading file: ${source}`);
        //     try {
        //         const response = await axios.get("/api/load-file", {
        //             params: { path: source },
        //             responseType: "blob",
        //         });
        //         load(response.data);
        //     } catch (err) {
        //         console.error("Failed to load file:", err);
        //         error("Failed to load file");
        //     }
        //     return {
        //         abort: () => {
        //             console.log("Load aborted");
        //             abort();
        //         },
        //     };
        // },
    }));

    onMounted(async () => {
        // try {
        //     const { data } = await axios.get("/api/get-uploaded-files");
        //     uploadedFiles.value = data.map((file) => ({
        //         source: file.path,
        //         options: { type: "local" },
        //     }));
        // } catch (error) {
        //     console.error("Failed to load previous uploads:", error);
        // }
        // FilePond.setOptions({
        //     server: {
        //         process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
        //             console.log('server called')
        //             // fieldName is the name of the input field
        //             // file is the actual file object to send
        //             const formData = new FormData();
        //             formData.append(fieldName, file, file.name);
        //             const request = new XMLHttpRequest();
        //             request.open('POST', 'url-to-api');
        //             // Should call the progress method to update the progress to 100% before calling load
        //             // Setting computable to false switches the loading indicator to infinite mode
        //             request.upload.onprogress = (e) => {
        //                 progress(e.lengthComputable, e.loaded, e.total);
        //             };
        //             // Should call the load method when done and pass the returned server file id
        //             // this server file id is then used later on when reverting or restoring a file
        //             // so your server knows which file to return without exposing that info to the client
        //             request.onload = function () {
        //                 if (request.status >= 200 && request.status < 300) {
        //                     // the load method accepts either a string (id) or an object
        //                     load(request.responseText);
        //                 } else {
        //                     // Can call the error method if something is wrong, should exit after
        //                     error('oh no');
        //                 }
        //             };
        //             request.send(formData);
        //             // Should expose an abort method so the request can be cancelled
        //             return {
        //                 abort: () => {
        //                     // This function is entered if the user has tapped the cancel button
        //                     request.abort();
        //                     // Let FilePond know the request has been cancelled
        //                     abort();
        //                 },
        //             };
        //         },
        //     },
        // });
        // this.$refs.field.on('processfile', (error, file) => {
        //     console.log("FilePond processed file:", file);
        // });
        // console.log(props, 'props');
        // field.value.node.on('input', ({ payload }) => {
        //     console.log('upload begun')
        //     props.context.node.store.filter(() => false);
        //     if (payload.length > props.context.limit) {
        //         props.context.node.store.set(
        //             createMessage({
        //                 blocking: true,
        //                 key: 'limit',
        //                 value: `You can only upload a maximum of ${props.context.limit} files.`,
        //             }),
        //         );
        //         return;
        //     }
        //     const size = props.context.size * 1000 * 1000;
        //     const uploads = [];
        //     for (const [index, value] of payload.entries()) {
        //         const extension = `.${value.file.name.split('.').pop()}`;
        //         // if (!props.context.accept.includes(extension)) {
        //         //     props.context.node.store.set(
        //         //         createMessage({
        //         //             blocking: true,
        //         //             key: `extension.${index}`,
        //         //             value: `The file extension ${extension} of ${value.file.name} is not supported.`,
        //         //         }),
        //         //     );
        //         //     return;
        //         // }
        //         if (value.file.size > size) {
        //             props.context.node.store.set(
        //                 createMessage({
        //                     blocking: true,
        //                     key: `size.${index}`,
        //                     value: `The file size of ${value.file.name} exceeds the maximum size of ${props.context.size}MB.`,
        //                 }),
        //             );
        //             return;
        //         }
        //     }
        //     payload.forEach((value, index) => {
        //         uploads.push(processUpload(value.file, index));
        //     });
        //     Promise.all(uploads).then((files) => {
        //         props.context.node.input(files);
        //     });
        // });
        // const processUpload = async (file, index) => {
        //     console.log('process upload begun');
        //     const { get } = consumer();
        //     props.context.node.store.set(
        //         createMessage({
        //             blocking: true,
        //             key: `uploading.${index}`,
        //             value: `Uploading ${file.name}...`,
        //         }),
        //     );
        //     return get(props.context.uploadUrl, {
        //         filename: file.name,
        //     })
        //         .then(async (response) => {
        //             const { url, path } = response.data;
        //             return axios
        //                 .put(url, file, {
        //                     headers: {
        //                         'Content-Type': file.type,
        //                     },
        //                 })
        //                 .then(() => {
        //                     props.context.node.store.set(
        //                         createMessage({
        //                             type: 'success',
        //                             key: `uploaded.${index}`,
        //                             value: `Uploaded ${file.name} successfully.`,
        //                         }),
        //                     );
        //                     return {
        //                         originalFileName: file.name,
        //                         path: path,
        //                     };
        //                 })
        //                 .catch(() => {
        //                     props.context.node.store.set(
        //                         createMessage({
        //                             blocking: true,
        //                             key: `uploaded.${index}`,
        //                             value: `Failed to upload ${file.name}.`,
        //                         }),
        //                     );
        //                     return null;
        //                 });
        //         })
        //         .catch(() => {
        //             props.context.node.store.set(
        //                 createMessage({
        //                     blocking: true,
        //                     key: `uploaded.${index}`,
        //                     value: `Failed to upload ${file.name}.`,
        //                 }),
        //             );
        //             return null;
        //         })
        //         .finally(() => {
        //             props.context.node.store.remove(`uploading.${index}`);
        //         });
        // };
    });
    const processUpload = async (file, index) => {
        console.log('process upload begun');
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
    // };
</script>

<template>
    <!-- <FormKit ref="field" type="file" accept="['.jpg']" :multiple="context.multiple" /> -->

    <!-- <FilePond ref="field" v-bind="context?.attrs" :label-idle="context?.attrs.placeholder"
        accepted-file-types="image/jpeg, image/png, text/html" /> -->
    <file-pond
        ref="field"
        label-idle="Drop files here or <span class='filepond--label-action'>Browse</span>"
        :allow-multiple="context.multiple"
        :accepted-file-types="context.accept.join(', ')"
        :maxFiles="context.limit"
        :maxFileSize="context.size + 'MB'"
        :files="uploadedFiles"
        :server="serverOptions"
    />

    <div :class="context.classes.help">Maximum number of files: {{ context.limit }}</div>
    <div :class="context.classes.help">Maximum file size: {{ context.size }} MB</div>
    <div :class="context.classes.help">Supported file extensions: {{ context.acceptnames?.join(', ') }}</div>
</template>

<style scoped></style>
