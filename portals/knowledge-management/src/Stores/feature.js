import { ref } from 'vue';
import { defineStore } from 'pinia';

export const useFeatureStore = defineStore('feature', () => {
    const hasServiceManagement = ref(false);

    async function setHasServiceManagement(value) {
        hasServiceManagement.value = value;
    }

    async function getHasServiceManagement() {
        return hasServiceManagement.value;
    }

    return {
        hasServiceManagement,
        getHasServiceManagement,
        setHasServiceManagement,
    };
});
