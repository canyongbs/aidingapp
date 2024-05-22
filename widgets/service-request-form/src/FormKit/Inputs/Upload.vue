<script setup>
import { onMounted, ref, watch } from "vue";

const props = defineProps({
    context: Object,
});

const field = ref(null);
const files = ref([]);

// console.log(props, props.context.node, field);

// watch(field, (value) => {
//     console.log(value);
//     // props.context.node.input(value);
// })

onMounted(() => {
    console.log(field, field.value.node.address);

    field.value.node.on('input', ({ payload }) => {
        props.context.node.clearErrors();
        props.context.node.input(payload);

        console.log(payload, field);
    });

    // field.value.node.hook.submit((payload, next) => {
    //     console.log(payload, next);
    // });

    props.context.node.at('$root').hook.submit((payload, next) => {

        console.log(payload, next);

        const address = field.value.node.address;
        address.shift();

        console.log(address.join('.'), payload, `payload.${address.join('.')}`);

        return next(payload);
    });
});

</script>

<template>
    <FormKit ref="field" type="file" :accept="context.accept" :multiple="context.multiple" />
</template>

<style scoped>

</style>
