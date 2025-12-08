<script setup>
import { reactive } from 'vue';
import { useAuthStore } from '@/stores/auth';

definePage({
    name: 'login',
    meta: {
        guest: true,
        layout: 'auth',
    },
});

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: '',
    password: '',
});


const handleLogin = async () => {
    const result = await authStore.login(form);
    if (result.success) {
        router.push('/dashboard');
    }
};
</script>
<template>
    <AuthLayout
        title="VirgoSoft"
        subtitle="Welcome back! Sign in to continue."
        variant="emerald"
    >
        <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Email Field -->
            <AppInput
                id="email"
                v-model="form.email"
                type="email"
                label="Email Address"
                placeholder="you@example.com"
                autocomplete="email"
                required
            >
                <template #icon>
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </template>
            </AppInput>

            <!-- Password Field -->
            <AppInput
                id="password"
                v-model="form.password"
                type="password"
                label="Password"
                placeholder="••••••••"
                autocomplete="current-password"
                :show-password-toggle="true"
                required
            >
                <template #icon>
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </template>
            </AppInput>

            <!-- Submit Button -->
            <AppButton
                type="submit"
                variant="primary"
                :loading="authStore.loading"
                loading-text="Signing in..."
                :show-arrow="true"
            >
                Sign In
            </AppButton>
        </form>

        <template #footer>
            <p class="text-slate-500">
                Don't have an account?
                <router-link to="/register" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">
                    Create one
                </router-link>
            </p>
        </template>
    </AuthLayout>
</template>


