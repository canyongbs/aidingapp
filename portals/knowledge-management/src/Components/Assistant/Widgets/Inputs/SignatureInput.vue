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
    import { VueSignaturePad } from 'vue-signature-pad';

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

    onMounted(() => {
        const fonts = document.createElement('link');
        fonts.type = 'text/css';
        fonts.rel = 'stylesheet';
        fonts.href = 'https://fonts.googleapis.com/css2?family=Satisfy&display=swap';

        document.head.appendChild(fonts);
    });

    const mode = ref('draw');
    const drawingPad = ref(null);
    const text = ref('');
    const signatureData = ref('');
    const error = ref('');

    const undoDrawing = () => {
        drawingPad.value.undoSignature();
    };

    const clearDrawing = () => {
        drawingPad.value.clearSignature();
        signatureData.value = '';
    };

    const saveDrawing = () => {
        const { data } = drawingPad.value.saveSignature();
        signatureData.value = data;
    };

    const resizeCanvas = () => {
        drawingPad.value.resizeCanvas();
    };

    watch(text, () => {
        const canvas = document.createElement('canvas');
        canvas.height = 200;
        canvas.width = 350;

        const canvasContext = canvas.getContext('2d');

        canvasContext.font = '48px Satisfy';
        canvasContext.fillText(text.value, 10, 100);

        signatureData.value = canvas.toDataURL();
    });

    const submit = () => {
        if (props.required && !signatureData.value) {
            error.value = 'Please provide a signature';
            return;
        }

        emit('submit', signatureData.value, 'Signature provided');
    };
</script>

<template>
    <div class="space-y-3">
        <div class="space-y-2">
            <label
                class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100 cursor-pointer"
                @click="mode = 'draw'"
            >
                <input
                    type="radio"
                    name="signature-mode"
                    value="draw"
                    v-model="mode"
                    class="h-4 w-4 text-brand-600 border-gray-300 focus:ring-brand-500"
                />
                <span class="text-sm text-gray-700">Draw it</span>
            </label>
            <label
                class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100 cursor-pointer"
                @click="mode = 'type'"
            >
                <input
                    type="radio"
                    name="signature-mode"
                    value="type"
                    v-model="mode"
                    class="h-4 w-4 text-brand-600 border-gray-300 focus:ring-brand-500"
                />
                <span class="text-sm text-gray-700">Type it</span>
            </label>
        </div>

        <div v-if="mode === 'draw'" class="space-y-2">
            <VueSignaturePad
                width="100%"
                height="200px"
                ref="drawingPad"
                :options="{ onBegin: resizeCanvas, onEnd: saveDrawing }"
                class="w-full border border-gray-300 rounded-md"
            />

            <div class="flex items-center gap-2">
                <button
                    @click="undoDrawing"
                    type="button"
                    class="px-3 py-1 text-xs text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    Undo
                </button>

                <button
                    @click="clearDrawing"
                    type="button"
                    class="px-3 py-1 text-xs text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    Clear
                </button>
            </div>
        </div>

        <div v-else-if="mode === 'type'">
            <input
                type="text"
                v-model="text"
                placeholder="Type your signature"
                class="w-full px-3 py-2 text-3xl text-center border border-gray-300 rounded-md focus:ring-brand-500 focus:border-brand-500"
                style="font-family: 'Satisfy', cursive; height: 200px"
                @input="error = ''"
            />
        </div>

        <p v-if="error" class="text-xs text-red-600">{{ error }}</p>

        <div class="flex gap-2">
            <button
                @click="submit"
                :disabled="required && !signatureData"
                class="flex-1 px-3 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Submit
            </button>
            <button
                @click="emit('cancel')"
                class="px-3 py-2 text-sm text-gray-600 bg-white hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors"
            >
                Cancel
            </button>
        </div>
    </div>
</template>
