<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const goHome = () => {
    router.push("/");
};

const goBack = () => {
    router.go(-1);
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-rose-500/10 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-violet-500/5 rounded-full blur-3xl"
            ></div>
        </div>

        <div class="relative w-full max-w-2xl text-center">
            <!-- Error Icon -->
            <div class="mb-8 flex justify-center">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-gradient-to-br from-rose-500/20 to-amber-500/20 border border-rose-500/30 mb-4 shadow-lg shadow-rose-500/10"
                >
                    <svg
                        class="w-12 h-12 text-rose-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                </div>
            </div>

            <!-- Error Content -->
            <div
                class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-2xl p-12 shadow-2xl"
            >
                <h1 class="text-9xl font-bold mb-4">
                    <span
                        class="bg-gradient-to-r from-rose-400 to-amber-400 bg-clip-text text-transparent"
                    >
                        4<span class="text-slate-600">0</span>4
                    </span>
                </h1>

                <h2 class="text-3xl font-bold text-white mb-3">
                    Oops! Page Not Found
                </h2>

                <p class="text-slate-400 text-lg mb-8 max-w-md mx-auto">
                    We're sorry, but the page you're looking for doesn't exist
                    or has been moved.
                </p>

                <!-- Action Buttons -->
                <div
                    class="flex flex-col sm:flex-row gap-4 justify-center items-center"
                >
                    <AppButton
                        variant="primary"
                        :show-arrow="true"
                        @click="goHome"
                    >
                        Go Home
                    </AppButton>

                    <AppButton variant="ghost" @click="goBack">
                        Go Back
                    </AppButton>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 text-slate-500 text-sm">
                <p>If you believe this is an error, please contact support.</p>
            </div>
        </div>
    </div>
</template>
