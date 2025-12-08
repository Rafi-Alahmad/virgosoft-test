import { defineStore } from 'pinia';
import { useApi } from '@/composables/useApi';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token') || null,
        loading: false,
    }),

    actions: {
        setToken(newToken) {
            const { setAuthToken } = useApi({ showToast: false });
            this.token = newToken;
            setAuthToken(newToken);
        },

        /**
         * Check if user is authenticated
         */
        async checkAuth() {
            if (!this.token) {
                this.user = null;
                return false;
            }

            const { get, setAuthToken } = useApi({ showToast: false });
            setAuthToken(this.token);

            const { data, error } = await get('/user');

            if (error) {
                this.setToken(null);
                this.user = null;
                return false;
            }

            this.user = data.user;
            return true;
        },

        async login(credentials) {
            this.loading = true;

            const { post } = useApi({});
            const { data, error } = await post('/login', credentials);

            if (error) {
                this.errors = error.errors || { general: [error.message] };
                this.loading = false;
                return { success: false, errors: this.errors };
            }

            this.user = data.user;
            this.setToken(data.token);
            this.loading = false;
            return { success: true };
        },

        async register(userData) {
            this.loading = true;

            const { post } = useApi({});
            const { data, error } = await post('/register', userData);

            if (error) {
                this.errors = error.errors || { general: [error.message] };
                this.loading = false;
                return { success: false, errors: this.errors };
            }

            this.user = data.user;
            this.setToken(data.token);
            this.loading = false;
            return { success: true };
        },

        async logout() {
            this.loading = true;

            const { post } = useApi({ showToast: false });
            await post('/logout');

            this.user = null;
            this.setToken(null);
            this.loading = false;
        },
    },

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,

        userInitials: (state) => {
            if (!state.user?.name) return '?';
            return state.user.name
                .split(' ')
                .map((n) => n[0])
                .join('')
                .toUpperCase()
                .slice(0, 2);
        },

        firstName: (state) => {
            if (!state.user?.name) return '';
            return state.user.name.split(' ')[0];
        },
    },
});
