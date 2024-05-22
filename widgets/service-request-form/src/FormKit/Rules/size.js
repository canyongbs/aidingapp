export default function size (node, size) {
    size *= 1024 * 1024;

    for (const value of node.value) {
        if(value.file.size > size) {
            return false;
        }
    }
    return true;
}
