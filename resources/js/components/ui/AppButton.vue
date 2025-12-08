<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'violet', 'danger', 'ghost'].includes(value),
    },
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loadingText: {
        type: String,
        default: 'Loading...',
    },
    showArrow: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = computed(() => {
    const variants = {
        primary: 'bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-white shadow-emerald-500/25 hover:shadow-emerald-500/40 focus:ring-emerald-500/50',
        secondary: 'bg-slate-800 hover:bg-slate-700 text-white shadow-slate-800/25 focus:ring-slate-500/50',
        violet: 'bg-gradient-to-r from-violet-500 to-fuchsia-500 hover:from-violet-400 hover:to-fuchsia-400 text-white shadow-violet-500/25 hover:shadow-violet-500/40 focus:ring-violet-500/50',
        danger: 'bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-400 hover:to-rose-400 text-white shadow-red-500/25 hover:shadow-red-500/40 focus:ring-red-500/50',
        ghost: 'bg-transparent hover:bg-slate-800 text-slate-400 hover:text-white shadow-none focus:ring-slate-500/50',
    };
    return variants[props.variant];
});
</script>
<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        class="w-full py-3.5 px-4 font-semibold rounded-xl shadow-lg focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]"
        :class="variantClasses"
    >
        <span v-if="!loading" class="flex items-center justify-center gap-2">
            <slot />
            <svg v-if="showArrow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </span>
        <span v-else class="flex items-center justify-center gap-2">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loadingText }}
        </span>
    </button>
</template>


