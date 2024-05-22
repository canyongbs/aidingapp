import { reactive, toRef, ref, watch } from 'vue';
import { getNode, createMessage } from '@formkit/core';

export default function upload() {
    const uploadPlugin = (node) => {
        if (node.props.type === 'file') {

            node.on('input', ({ payload }) => {
                console.log(payload);
            });

            // Stop plugin inheritance to descendant nodes
            return false;
        }
    };

    return { uploadPlugin };
}
