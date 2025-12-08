<script setup>
import { reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "@/composables/useToast";

definePage({
    name: "register",
    meta: {
        guest: true,
        layout: "auth",
    },
});

const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const handleRegister = async () => {
    const result = await authStore.register(form);
    if (result.success) {
        toast.success("Account created successfully!");
        router.push({name: "dashboard"});
    }
};
</script>

<template>
    <AuthLayout
        title="Join VirgoSoft"
        subtitle="Create your account to get started."
        variant="violet"
    >
        <template #logo>
            <svg
                class="w-8 h-8 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                />
            </svg>
        </template>

        <form @submit.prevent="handleRegister" class="space-y-5">
            <!-- Name Field -->
            <AppInput
                id="name"
                v-model="form.name"
                type="text"
                label="Full Name"
                placeholder="John Doe"
                autocomplete="name"
                color="violet"
                required
            >
                <template #icon>
                    <svg
                        class="w-5 h-5 text-slate-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                </template>
            </AppInput>

            <!-- Email Field -->
            <AppInput
                id="email"
                v-model="form.email"
                type="email"
                label="Email Address"
                placeholder="you@example.com"
                autocomplete="email"
                color="violet"
                required
            >
                <template #icon>
                    <svg
                        class="w-5 h-5 text-slate-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                        />
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
                autocomplete="new-password"
                :show-password-toggle="true"
                color="violet"
                required
            >
                <template #icon>
                    <svg
                        class="w-5 h-5 text-slate-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        />
                    </svg>
                </template>
            </AppInput>

            <!-- Confirm Password Field -->
            <AppInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                label="Confirm Password"
                placeholder="••••••••"
                autocomplete="new-password"
                :show-password-toggle="true"
                color="violet"
                required
            >
                <template #icon>
                    <svg
                        class="w-5 h-5 text-slate-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                        />
                    </svg>
                </template>
            </AppInput>

            <!-- Submit Button -->
            <AppButton
                type="submit"
                variant="violet"
                :loading="authStore.loading"
                loading-text="Creating account..."
                :show-arrow="true"
            >
                Create Account
            </AppButton>
        </form>

        <template #footer>
            <p class="text-slate-500">
                Already have an account?
                <router-link
                    to="/login"
                    class="text-violet-400 hover:text-violet-300 font-medium transition-colors"
                >
                    Sign in
                </router-link>
            </p>
        </template>
    </AuthLayout>
</template>
