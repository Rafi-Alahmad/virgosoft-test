import { useAuthStore } from '@/stores/auth';

export const setupGuards = (router) => {
    router.beforeEach(async (to, from, next) => {
        const authStore = useAuthStore();

        // Check auth status on protected routes
        if (to.meta.requiresAuth) {
            await authStore.checkAuth();
            if (!authStore.isAuthenticated) {
                return next("/login");
            }
        }

        // Redirect authenticated users away from guest pages
        if (to.meta.guest) {
            await authStore.checkAuth();
            if (authStore.isAuthenticated) {
                return next("/dashboard");
            }
        }

        next();
    });
};
