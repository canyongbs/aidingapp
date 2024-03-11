import axios from '@/Globals/Axios.js';
import { useTokenStore } from '@/Stores/token.js';

export function consumer() {
    async function get(endpoint, data = null) {
        const { getToken } = useTokenStore();

        let token = await getToken();

        return await axios
            .get(endpoint, {
                headers: { Authorization: `Bearer ${token}` },
                params: data,
            })
            .then((response) => {
                return response;
            })
            .catch((error) => {
                return Promise.reject(error);
            });
    }

    async function post(endpoint, data) {
        const { getToken } = useTokenStore();

        let token = await getToken();

        return await axios
            .post(endpoint, data, {
                headers: { Authorization: `Bearer ${token}` },
            })
            .then((response) => {
                return response;
            })
            .catch((error) => {
                return Promise.reject(error);
            });
    }

    return { get, post };
}
