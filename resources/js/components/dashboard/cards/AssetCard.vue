<script setup>
import { computed } from 'vue';
import StatCard from '@/components/ui/StatCard.vue';

const props = defineProps({
    symbol: {
        type: String,
        required: true,
    },
    amount: {
        type: [String, Number],
        default: 0,
    },
    lockedAmount: {
        type: [String, Number],
        default: 0,
    },
});

const availableAmount = computed(() => {
    const total = parseFloat(props.amount) || 0;
    const locked = parseFloat(props.lockedAmount) || 0;
    return total - locked;
});

const colorMap = computed(() => {
    const colors = ['emerald', 'violet', 'amber', 'cyan', 'rose'];
    const index = props.symbol.charCodeAt(0) % colors.length;
    return colors[index];
});
</script>

<template>
    <StatCard
        :value="formatAmount(availableAmount)"
        :label="`${symbol} Available`"
        :badge="symbol"
        :color="colorMap"
    >
        <template #icon>
            <svg
                class="w-6 h-6"
                :class="{
                    'text-emerald-400': colorMap === 'emerald',
                    'text-violet-400': colorMap === 'violet',
                    'text-amber-400': colorMap === 'amber',
                    'text-cyan-400': colorMap === 'cyan',
                    'text-rose-400': colorMap === 'rose',
                }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
        </template>
    </StatCard>
</template>

