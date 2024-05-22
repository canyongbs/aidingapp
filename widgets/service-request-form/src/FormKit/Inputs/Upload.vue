<script setup>
import { onMounted, ref, watch } from "vue";
import { createMessage } from "@formkit/core";

const props = defineProps({
    context: Object,
    uploadUrl: String,
});

const field = ref(null);
const files = ref([]);

console.log(props, props.context.node, field);

// watch(field, (value) => {
//     console.log(value);
//     // props.context.node.input(value);
// })

onMounted(() => {
    // console.log(field, field.value.node.address);

    field.value.node.on('input', ({ payload }) => {
        props.context.node.store.filter(() => false);
        props.context.node.input(payload);

        console.log(payload);

        if (payload.length > props.context.limit) {
            props.context.node.store.set(createMessage({
                blocking: true,
                key: 'limit',
                value: `You can only upload a maximum of ${props.context.limit} files.`,
            }));

            return;
        }

        const size = props.context.size * 1024 * 1024;

        payload.forEach((value, index) => {
            if (value.file.size > size) {
                props.context.node.store.set(createMessage({
                    blocking: true,
                    key: `size.${index}`,
                    value: `The file size of ${value.file.name} exceeds the maximum size of ${props.context.size}MB.`,
                }));
            }
        });
    });

    field.value.node.hook.submit((payload, next) => {
        console.log(payload, next);
    });

    // props.context.node.at('$root').hook.submit((payload, next) => {
    //
    //     console.log(payload, next);
    //
    //     const address = field.value.node.address;
    //     address.shift();
    //
    //     console.log(address.join('.'), payload, `payload.${address.join('.')}`);
    //
    //     return next(payload);
    // });
});

</script>

<template>
    <FormKit ref="field" type="file" :accept="context.accept" :multiple="context.multiple" />
</template>

<style scoped>

</style>
