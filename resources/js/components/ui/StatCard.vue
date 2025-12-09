<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    badge: {
        type: String,
        default: '',
    },
    color: {
        type: String,
        default: 'emerald',
        validator: (value) => ['emerald', 'violet', 'amber', 'cyan', 'rose'].includes(value),
    },
});

const colorClasses = computed(() => {
    const colors = {
        emerald: 'bg-gradient-to-br from-emerald-500/10 to-cyan-500/10 border-emerald-500/20 hover:border-emerald-500/40',
        violet: 'bg-gradient-to-br from-violet-500/10 to-fuchsia-500/10 border-violet-500/20 hover:border-violet-500/40',
        amber: 'bg-gradient-to-br from-amber-500/10 to-orange-500/10 border-amber-500/20 hover:border-amber-500/40',
        cyan: 'bg-gradient-to-br from-cyan-500/10 to-blue-500/10 border-cyan-500/20 hover:border-cyan-500/40',
        rose: 'bg-gradient-to-br from-rose-500/10 to-pink-500/10 border-rose-500/20 hover:border-rose-500/40',
    };
    return colors[props.color];
});

const hoverBgClass = computed(() => {
    const colors = {
        emerald: 'bg-gradient-to-br from-emerald-500/5 to-cyan-500/5',
        violet: 'bg-gradient-to-br from-violet-500/5 to-fuchsia-500/5',
        amber: 'bg-gradient-to-br from-amber-500/5 to-orange-500/5',
        cyan: 'bg-gradient-to-br from-cyan-500/5 to-blue-500/5',
        rose: 'bg-gradient-to-br from-rose-500/5 to-pink-500/5',
    };
    return colors[props.color];
});

const iconBgClass = computed(() => {
    const colors = {
        emerald: 'bg-emerald-500/20',
        violet: 'bg-violet-500/20',
        amber: 'bg-amber-500/20',
        cyan: 'bg-cyan-500/20',
        rose: 'bg-rose-500/20',
    };
    return colors[props.color];
});

const badgeClasses = computed(() => {
    const colors = {
        emerald: 'text-emerald-400 bg-emerald-500/10',
        violet: 'text-violet-400 bg-violet-500/10',
        amber: 'text-amber-400 bg-amber-500/10',
        cyan: 'text-cyan-400 bg-cyan-500/10',
        rose: 'text-rose-400 bg-rose-500/10',
    };
    return colors[props.color];
});
</script>
<template>
    <div
        class="group relative rounded-2xl p-6 border transition-all duration-300"
        :class="colorClasses"
    >
        <div
            class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"
            :class="hoverBgClass"
        ></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="iconBgClass">
                    <slot name="icon" />
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-lg" :class="badgeClasses">
                    {{ badge }}
                </span>
            </div>
            <p class="text-3xl font-bold text-white mb-1 overflow-hidden min-w-0">
                <slot name="value">{{ value }}</slot>
            </p>
            <p class="text-slate-500 text-sm">{{ label }}</p>
        </div>
    </div>
</template>


