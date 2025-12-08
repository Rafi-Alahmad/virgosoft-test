<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: "login" });
};
</script>
<template>
    <nav class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">
                            VirgoSoft
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- User Info -->
                    <div class="hidden sm:flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-800/50 border border-slate-700">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-400 to-fuchsia-500 flex items-center justify-center text-white text-sm font-bold">
                            {{ authStore.userInitials }}
                        </div>
                        <div class="text-sm">
                            <p class="text-white font-medium">{{ authStore.user?.name }}</p>
                            <p class="text-slate-500 text-xs">{{ authStore.user?.email }}</p>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <button
                        @click="handleLogout"
                        :disabled="authStore.loading"
                        class="flex items-center gap-2 px-4 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

