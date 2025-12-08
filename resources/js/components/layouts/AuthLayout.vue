<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'VirgoSoft',
    },
    subtitle: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'emerald',
        validator: (value) => ['emerald', 'violet'].includes(value),
    },
});

const bgBlobColor1 = computed(() => {
    return props.variant === 'emerald' ? 'bg-emerald-500/10' : 'bg-violet-500/10';
});

const bgBlobColor2 = computed(() => {
    return props.variant === 'emerald' ? 'bg-cyan-500/10' : 'bg-fuchsia-500/10';
});

const bgBlobColor3 = computed(() => {
    return props.variant === 'emerald' ? 'bg-violet-500/5' : 'bg-emerald-500/5';
});

const logoClasses = computed(() => {
    return props.variant === 'emerald'
        ? 'bg-gradient-to-br from-emerald-400 to-cyan-500 shadow-emerald-500/25'
        : 'bg-gradient-to-br from-violet-400 to-fuchsia-500 shadow-violet-500/25';
});
</script>

<template>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-40 -right-40 w-80 h-80 rounded-full blur-3xl"
                :class="bgBlobColor1"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full blur-3xl"
                :class="bgBlobColor2"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full blur-3xl"
                :class="bgBlobColor3"
            ></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Logo/Brand -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 shadow-lg"
                    :class="logoClasses"
                >
                    <slot name="logo">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </slot>
                </div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent tracking-tight">
                    {{ title }}
                </h1>
                <p class="text-slate-500 mt-2">{{ subtitle }}</p>
            </div>

            <!-- Content Card -->
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-2xl p-8 shadow-2xl">
                <slot />

                <!-- Footer Link -->
                <div v-if="$slots.footer" class="mt-8 pt-6 border-t border-slate-800 text-center">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </div>
</template>

